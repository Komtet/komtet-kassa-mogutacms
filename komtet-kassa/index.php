<?php
/*
	Plugin Name: Онлайн касса КОМТЕТ Касса
	Description: Плагин для отправки электронных чеков и фискализации по 54-ФЗ
	Author: КОМТЕТ Касса
	Version: 1.2.0
*/

require PLUGIN_DIR.'komtet-kassa/lib/komtet-kassa-php-sdk/autoload.php';

use Komtet\KassaSdk\CalculationMethod;
use Komtet\KassaSdk\CalculationSubject;
use Komtet\KassaSdk\Client;
use Komtet\KassaSdk\QueueManager;
use Komtet\KassaSdk\Check;
use Komtet\KassaSdk\Payment;
use Komtet\KassaSdk\Position;
use Komtet\KassaSdk\Vat;
use Komtet\KassaSdk\Exception\SdkException;

new KomtetKassa;

class KomtetKassa{

    const CARD = 'Безналичный рассчет';
    const CASH = 'Наличный рассчет';
    const PREPAYMENT = 'Предоплата';
    const CLOSED = 'closed';
    const ORDER_STATUS_MAP = array(
        'not_confirm' => '0',
        'awaiting_payment' => '1',
        'paid' => '2',
        'in_delivery' => '3',
        'returned' => '4',
        'completed' => '5',
        'in_processing' => '6'
    );
    const CALCULATION_METHOD = array(
        self::CLOSED => CalculationMethod::FULL_PAYMENT,
        CalculationMethod::PRE_PAYMENT_FULL => CalculationMethod::PRE_PAYMENT_FULL,
        CalculationMethod::FULL_PAYMENT =>  CalculationMethod::FULL_PAYMENT,
    );
    const CALCULATION_SUBJECT = array(
        CalculationMethod::PRE_PAYMENT_FULL => CalculationSubject::PAYMENT,
        self::CLOSED => CalculationSubject::PRODUCT,
        CalculationMethod::FULL_PAYMENT => CalculationSubject::PRODUCT,
    );
    const PAYMENTS_METHODS = array(
        self::CARD => Payment::TYPE_CARD,
        self::CASH => Payment::TYPE_CASH,
        self::PREPAYMENT => Payment::TYPE_PREPAYMENT,
    );
    private static $pluginName = '';
    private static $path = '';

    /**
     * В конструктор класса происходит инициализация всех необходимых данных для работы плагина, таких как:
     * -системное имя плагина;
     * -локали;
     * -шорткоды;
     * -страница настроек плагина и активация плагина;
     * -создание страницы для публички;
     * -подключение css и js для публички;
     */
    public function __construct() {

        self::$pluginName = PM::getFolderPlugin(__FILE__);
        self::$path = PLUGIN_DIR.self::$pluginName;

        mgActivateThisPlugin(__FILE__, array(__CLASS__, 'activate'));  // Активация плагина
        mgAddAction(__FILE__, array(__CLASS__, 'pageSettingsPlugin'));  // Настройки плагина
        mgAddAction('Models_Order_updateOrder', array(__CLASS__, 'updateOrder'),1);  // Подписка на хук(обновление заказа)
        mgAddAction('Models_Order_addOrder', array(__CLASS__, 'createOrder'),1);  // Подписка на хук(созадние заказа)
    }

    /**
     * Метод выполняющийся при активации палагина
     * Создает таблицу в базе данных и опции
     * Используемые методы:
     *  createDateBase()
     *  createOptions()
     */
    static function activate() {
        self::createOptions();
        self::createDateBase();

    }

    static function createOptions() {
        /**
         * komtet-kassa-option - поле, содержащее значения настроек плагина (shop_id, queue_id ...)
         * komtet-kassa-payment-option - поле, содержащее настройки способов оплаты
         */

        if (MG::getSetting('komtet-kassa-option') == null) {
            MG::setOption(array('option' => 'komtet-kassa-option', 'value' => ''));
            MG::setOption(array('option' => 'komtet-kassa-payment-option', 'value' => ''));
        }
    }

    static function createDateBase() {

        DB::query("
          CREATE TABLE IF NOT EXISTS `".PREFIX."kk_order` (
            `id` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Порядковый номер',
            `order_id` INT NOT NULL UNIQUE COMMENT 'Идентификатор заказа',
            `check_type` VARCHAR(25) COMMENT 'Тип выданного чека',
            `is_paid` TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Флаг оплаты заказа',
            `fulfillment_status_id` INT COMMENT 'Идентификатор статуса выданного чека',
            `request` TEXT,
            `response` TEXT,

            PRIMARY KEY (`id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
        ");

        DB::query("
          CREATE TABLE IF NOT EXISTS `".PREFIX."kk_report` (
            `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Порядковый номер записи',
            `order_id` int(11) NOT NULL,
            `fisc_state` varchar(255) COMMENT 'Состояние задачи',
            `error_description` varchar(255) COMMENT 'Описание возникшей ошибки',

            PRIMARY KEY (`id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
        ");
    }

    /**
     * Подключение стилей и js
     */
    static function preparePageSettings() {
        $path = PLUGIN_DIR.PM::getFolderPlugin(__FILE__);  // папка плагина
        echo '
            <link rel="stylesheet" href="'.SITE.'/'.$path.'/css/style.css" type="text/css" />

            <script>
                includeJS("'.SITE.'/'.$path.'/js/script.js");
            </script>
        ';
    }

    /**
     * Выводит страницу настроек плагина в админке
     *
     * Используемые методы:
     *  preparePageSettings()
     *
     */
    static function pageSettingsPlugin() {
        self::preparePageSettings();

        $options = unserialize(stripslashes(MG::getSetting('komtet-kassa-option')));
        $savedPayments  = unserialize(stripslashes(MG::getSetting('komtet-kassa-payment-option')));

        $rows = DB::query("SELECT `id`, `name` FROM `".PREFIX."payment` WHERE `activity` = TRUE ORDER BY `sort` asc");
        while ($row = DB::fetchAssoc($rows)) {
            $paymentVariants[$row['id']] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'hash' => md5($row['name'].$row['id'])
            ];
        }

        if ($savedPayments) {
            foreach($paymentVariants as $payment) {
                foreach($savedPayments as $savedPayment) {
                    if ($savedPayment['payId'] == $payment['id']) {
                        $paymentVariants[(int)$savedPayment['payId']] += [
                            'active' => true, 'option' => $savedPayment['option']
                        ];

                        break;
                    }
                }
            }
        }

        include 'pageplugin.php';
    }

    static function createOrder($args){
        /**
        * Чек будет фискализирован:
        *   - созданный заказ оплачен(установлен флаг "Оплачен");
        *   - способ оплаты совпадает с выбранными настройками на странице плагина.
        */
        $mogutaOrder = $args['args'][0];
        $orderId = $args['origResult']['id'];
        $orderNumber = $mogutaOrder['number'];

        $pluginSettings = unserialize(stripslashes(MG::getSetting('komtet-kassa-option')));
        if ($mogutaOrder['status_id'] != self::ORDER_STATUS_MAP['paid']) {
            return false;
        }

        $paymentOptions  = unserialize(stripslashes(MG::getSetting('komtet-kassa-payment-option')));
        foreach($paymentOptions as $payment) {
            if ($payment['payId'] == $mogutaOrder['payment_id']) {
                $paymentType = $payment['option'];

                break;
            }
        }

        if ($paymentType) {
            if (unserialize(stripslashes(MG::getSetting('komtet-kassa-option')))['is_prepayment_check'] == 'true') {
                $checkType = CalculationMethod::PRE_PAYMENT_FULL;
            } else {
                $checkType = CalculationMethod::FULL_PAYMENT;
            }

            try {
                $check = self::buildCheck($orderNumber, $mogutaOrder, $paymentType, $checkType);
            } catch (Exception $e) {
                MG::loger("Ошибка при сборке чека по заказу № - ". $orderNumber. ', id - ' . $orderId);
                return false;
            }

            if (!self::fiscalizeOrder($pluginSettings, $check)) {
                return false;
            }

            DB::query("
                INSERT IGNORE INTO `".PREFIX."kk_order` (`order_id`, `check_type`, `is_paid`, `fulfillment_status_id`)
                VALUES (
                  ".DB::quoteInt($orderId).",
                  ".DB::quote($checkType).",
                  ".DB::quoteInt(1).",
                  ".DB::quoteInt($mogutaOrder['status_id'])."
                )
            ");

        }

        return true;
    }

    static function updateOrder($args) {
        $mogutaOrder = $args['args'][0];
        $orderId = $args['args'][0]['id'];
        $orderNumber = $mogutaOrder['number'];

        $pluginSettings = unserialize(stripslashes(MG::getSetting('komtet-kassa-option')));
        $queryOrder = DB::query("SELECT * FROM `".PREFIX."kk_order` WHERE `order_id` = " .DB::quote($orderId));
        if (!$queryOrder->num_rows) {
            DB::query("
                INSERT IGNORE INTO `".PREFIX."kk_order` (`order_id`)
                VALUES (
                  ".DB::quoteInt($orderId)."
                )
            ");
        }
        $order = DB::fetchAssoc($queryOrder);

        $unhandledOrder = ($mogutaOrder['status_id'] != self::ORDER_STATUS_MAP['paid'] and !$order['is_paid']);
        $paidOrder = (
            $order['is_paid'] and $mogutaOrder['status_id'] != self::ORDER_STATUS_MAP['returned'] and
            $mogutaOrder['status_id'] != $pluginSettings['fullpayment_check_status']

        );
        $returnedOrder = $order['fulfillment_status_id'] == self::ORDER_STATUS_MAP['returned'];
        $closedOrder = ($mogutaOrder['status_id'] == $pluginSettings['fullpayment_check_status'] and
                        $order['fulfillment_status_id'] == $pluginSettings['fullpayment_check_status']);

        if ($unhandledOrder or $paidOrder or $returnedOrder or $closedOrder) {
            return false;
        }

        if ($mogutaOrder['status_id'] == self::ORDER_STATUS_MAP['returned'] && !$order['check_type']) {
            return false;
        }

        foreach($pluginSettings['payments'] as $payment) {
            if ($payment['payId'] == $mogutaOrder['payment_id']) {
                $paymentType = $payment['option'];

                break;
            }
        }

        if ($paymentType) {
            $checkType = CalculationMethod::FULL_PAYMENT;
            // Если необходимо выдавать 2 чека
            if ($pluginSettings['is_prepayment_check'] == 'true') {
                $checkType = CalculationMethod::PRE_PAYMENT_FULL;
                // Если заказ уже оплачен и получен статус закрывающего чека => формируем закрывающий чек
                if ($order['is_paid'] and  $mogutaOrder['status_id'] == $pluginSettings['fullpayment_check_status']) {
                    $checkType = self::CLOSED;
                }
            }

            $isReturn = false;
            if ($mogutaOrder['status_id'] == self::ORDER_STATUS_MAP['returned']) {
                $isReturn = true;
            }

            try {
                $check = self::buildCheck(
                    $orderNumber,
                    $mogutaOrder,
                    $paymentType,
                    $isReturn ? $order['check_type'] : $checkType,
                    $isReturn
                );
            } catch (Exception $e) {
                MG::loger("Ошибка при сборке чека " . ($isReturn ? "Возврата" : "") . " по заказу№ - ". $orderNumber. ', id - ' . $orderId);
                return false;
            }

            if ($check) {
                if (!self::fiscalizeOrder($pluginSettings, $check)) {
                    return false;
                }

                DB::query(
                    "UPDATE `".PREFIX."kk_order`
                     SET `is_paid` = " .DB::quoteInt(1) ."
                     WHERE `order_id` = " .DB::quoteInt($orderId)
                );

                if ($checkType != CalculationMethod::PRE_PAYMENT_FULL or
                    $mogutaOrder['status_id'] == self::ORDER_STATUS_MAP['returned']) {

                    DB::query(
                        "UPDATE `".PREFIX."kk_order`
                         SET `fulfillment_status_id` = " .DB::quoteInt($mogutaOrder['status_id']) ."
                         WHERE `order_id` = " .DB::quoteInt($orderId)
                    );
                }

                if ($mogutaOrder['status_id'] == self::ORDER_STATUS_MAP['returned']) {
                    DB::query(
                        "UPDATE `".PREFIX."kk_order`
                         SET `check_type` = " .DB::quote($order['check_type']) ."
                         WHERE `order_id` = " .DB::quoteInt($orderId)
                    );
                } else {
                    DB::query(
                        "UPDATE `".PREFIX."kk_order`
                         SET `check_type` = " .DB::quote($checkType) ."
                         WHERE `order_id` = " .DB::quoteInt($orderId)
                    );
                }
            }
        }

        return true;
    }

    /**
     * Формирование чека
     *
     * Параметры:
     *  orderNumber - внутренний номер заказа;
     *  mogutaOrder - заказ;
     *  paymentType - тип оплаты (Наличный/безналичный рассчёт);
     *  checkType - тип формируемого чека;
     *  isReturning - возврат.
     *
     */
    static function buildCheck($orderNumber, $mogutaOrder, $paymentType, $checkType, $isReturning=false) {
        $user = ($mogutaOrder['contact_email']) ? $mogutaOrder['contact_email'] : $mogutaOrder['phone'];
        $pluginSettings = unserialize(stripslashes(MG::getSetting('komtet-kassa-option')));

        if (!$isReturning) {
            $check = Check::createSell($orderNumber, $user, (int)$pluginSettings['sno']);
        } else {
            $check = Check::createSellReturn($orderNumber, $user, (int)$pluginSettings['sno']);
        }

        $print_check = ($pluginSettings['is_print'] === 'true');
        $check->setShouldPrint($print_check); // печать чека на ккт

        $unserializePositions = unserialize(stripslashes($mogutaOrder['order_content']));
        if ($unserializePositions) {
            $mogutaOrder['order_content'] = $unserializePositions;
        }

        foreach ($mogutaOrder['order_content'] as $orderItem) {
            $vat = new Vat($pluginSettings['vat']);
            $position = self::generatePosition($orderItem, $orderItem['count'], $vat, $checkType);
            $check->addPosition($position);
        }

        if ((float)$mogutaOrder['delivery_cost'] > 0) {
            $vatDelivery = new Vat($pluginSettings['vat_delivery']);
            $queryOrder = DB::query(
                "SELECT *
                 FROM `".PREFIX."delivery`
                 WHERE `id` = " .DB::quote($mogutaOrder['delivery_id'])
            );
            $delivery = DB::fetchAssoc($queryOrder);

            $positionDelivery = new Position(
                "Доставка " . $delivery['name'],
                round($mogutaOrder['delivery_cost'], 2),
                1,
                round($mogutaOrder['delivery_cost'], 2),
                $vatDelivery
            );
            $positionDelivery->setId($delivery['id']);
            $positionDelivery->setCalculationMethod(self::CALCULATION_METHOD[$checkType]);
            $positionDelivery->setCalculationSubject(self::CALCULATION_SUBJECT[$checkType]);
            $check->addPosition($positionDelivery);
        }

        $payment = new Payment(
            $checkType == 'closed' && !$isReturning ? self::PAYMENTS_METHODS[self::PREPAYMENT] :
                                                      self::PAYMENTS_METHODS[$paymentType],
            round((float)($mogutaOrder['delivery_cost'] + $mogutaOrder['summ']), 2));
        $check->addPayment($payment);

        return $check;
    }

    private static function generatePosition($item, $quantity, $vat, $checkType) {
        $itemTotal = $item['price'] * $quantity;

        if ($item['count'] != $quantity) {
            $itemTotal = $itemTotal - $item['discount']/$item['count'];
        } else {
            $itemTotal = $itemTotal - $item['discount'];
        }

        $position = new Position(
            $item['name'],
            round($item['price'], 2),
            round($item['count'], 2),
            round($itemTotal, 2),
            $vat
        );
        $position->setId($item['id']);
        $position->setCalculationMethod(self::CALCULATION_METHOD[$checkType]);
        $position->setCalculationSubject(self::CALCULATION_SUBJECT[$checkType]);

        return $position;
    }

    static function fiscalizeOrder($pluginSettings, $check) {
        $client = new Client($pluginSettings['shop_id'], $pluginSettings['secret']);
        $manager = new QueueManager($client);

        $manager->registerQueue('ss-queue', $pluginSettings['queue_id']);

        try {
            return $manager->putCheck($check, 'ss-queue');
        } catch (Exception $e) {
            MG::loger(
                "Ошибка фискализации заказа.
                 [Ответ - ".$e->getMessage().". ".$e->getDescription()."]
                 [Код КК - ".$e->getVLDCode()."]"
            );

            DB::query(
                "UPDATE `".PREFIX."kk_order`
                 SET `request` = " .DB::quote(serialize($check->asArray()))."
                 WHERE `order_id` = " .DB::quoteInt($check->asArray()['external_id'])
            );
            DB::query(
                "UPDATE `".PREFIX."kk_order`
                 SET `response` = " .DB::quote($e->getMessage().". ".$e->getDescription()) ."
                 WHERE `order_id` = " .DB::quoteInt($check->asArray()['external_id'])
            );

            return false;
        }
    }
}
