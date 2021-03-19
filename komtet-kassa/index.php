<?php
/*
	Plugin Name: Онлайн касса КОМТЕТ Касса
	Description: Плагин для отправки электронных чеков и фискализации по 54-ФЗ
	Author: КОМТЕТ Касса
	Version: 1.0.0
*/

require PLUGIN_DIR.'komtet-kassa/lib/komtet-kassa-php-sdk/autoload.php';

use Komtet\KassaSdk\CalculationMethod;
use Komtet\KassaSdk\CalculationSubject;

new KomtetKassa;

class KomtetKassa{

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
        /**
         * В таблице `mg_order` создаются поля `check_type` и `is_fiscalized`
         * Создается таблица `mg_komtet_kassa_reports`, в которой будут хранится отчёты по фискализированным чекам
         */

        DB::query("ALTER TABLE `".PREFIX.order."` ADD COLUMN `check_type` VARCHAR(25) AFTER `pay_date`", $noError = true);
        DB::query("ALTER TABLE `".PREFIX.order."` ADD COLUMN `is_fiscalized` TINYINT(1) NOT NULL DEFAULT 0 AFTER `check_type`", $noError = true);
        DB::query("ALTER TABLE `".PREFIX.order."` ADD COLUMN `is_paid` TINYINT(1) NOT NULL DEFAULT 0 AFTER `is_fiscalized`", $noError = true);
        DB::query("ALTER TABLE `".PREFIX.order."` ADD COLUMN `fulfillment_status_id` INT(11) AFTER `is_paid`", $noError = true);
        DB::query("ALTER TABLE `".PREFIX.order."` ADD COLUMN `request` JSON", $noError = true);
        DB::query("ALTER TABLE `".PREFIX.order."` ADD COLUMN `response` JSON AFTER `request`", $noError = true);



        DB::query("
          CREATE TABLE IF NOT EXISTS `".PREFIX."komtet_kassa_reports` (
            `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Порядковый номер записи',
            `order_id` int(11) NOT NULL,
            `fisc_state` varchar(255) COMMENT 'Состояние задачи',
            `error_description` varchar(255) COMMENT 'Описание возникшей ошибки',

            PRIMARY KEY (`id`)
          ) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
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
                'hash' => md5($row['name'])
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
        $orderId = $args['origResult']['id'];
        $mogutaOrder = $args['args'][0];

        if (!$mogutaOrder['paided']) {
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
                $check = self::buildCheck($orderId, $mogutaOrder, $paymentType, $checkType);
            } catch (Exception $e) {
                mg::loger("Ошибка при сборке чека по заказу - ". $orderId);
                return false;
            }

            try {
                self::fiscalizeOrder($orderId, $mogutaOrder, $check, $checkType);
            } catch (Exception $e) {
                mg::loger("Ошибка при фискализации чека по заказу - ". $orderId);
                return false;
            }

            DB::query(
                "UPDATE `".PREFIX."order` SET `is_paid` = " .DB::quoteInt(1) ."WHERE `id` = " .DB::quoteInt($orderId)
            );
            DB::query(
                "UPDATE `".PREFIX."order`
                 SET `check_type` = " .DB::quote($checkType) ."
                 WHERE `id` = " .DB::quoteInt($orderId)
            );
         }

        return true;
    }

    static function updateOrder($args) {
        $mogutaOrder = $args['args'][0];
        $numberOrder = $mogutaOrder['number'];

        $pluginSettings = unserialize(stripslashes(MG::getSetting('komtet-kassa-option')));
        $queryOrder = DB::query("SELECT * FROM `".PREFIX."order` WHERE `number` = " .DB::quote($numberOrder));
        $order = DB::fetchAssoc($queryOrder);

        $unhandledOrder = (!$mogutaOrder['paided'] and !$order['is_paid']);
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

            try {
                $check = self::buildCheck(
                    $order['id'], $mogutaOrder, $paymentType, $checkType,
                    $mogutaOrder['status_id'] == self::ORDER_STATUS_MAP['returned']
                );
            } catch (Exception $e) {
                mg::loger("Ошибка при сборке чека по заказу - ". $order['id']);
            }

            if ($check) {
                try {
                    self::fiscalizeOrder($orderId, $mogutaOrder, $check, $checkType);
                } catch (Exception $e) {
                    mg::loger("Ошибка фискализации заказа. [Заказ - ".$order['id']."][Ответ - ".$e."]" );

                    return false;
                }

                DB::query(
                    "UPDATE `".PREFIX."order`
                     SET `is_paid` = " .DB::quoteInt(1) ."
                     WHERE `id` = " .DB::quoteInt($order['id'])
                );

                if ($checkType != CalculationMethod::PRE_PAYMENT_FULL or
                    $mogutaOrder['status_id'] == self::ORDER_STATUS_MAP['returned']) {

                    DB::query(
                        "UPDATE `".PREFIX."order`
                         SET `fulfillment_status_id` = " .DB::quoteInt($mogutaOrder['status_id']) ."
                         WHERE `id` = " .DB::quoteInt($order['id'])
                    );
                }

                if ($mogutaOrder['status_id'] == self::ORDER_STATUS_MAP['returned']) {
                    DB::query(
                        "UPDATE `".PREFIX."order`
                         SET `check_type` = " .DB::quote($order['check_type']) ."
                         WHERE `id` = " .DB::quoteInt($order['id'])
                    );
                } else {
                    DB::query(
                        "UPDATE `".PREFIX."order`
                         SET `check_type` = " .DB::quote($checkType) ."
                         WHERE `id` = " .DB::quoteInt($order['id'])
                    );
                }
            }
        }

        return true;
    }

    static function buildCheck($orderId, $mogutaOrder, $paymentType, $checkType, $returning=false) {

        return true;
    }

    static function fiscalizeOrder($orderId, $mogutaOrder, $check, $checkType) {

        return true;
    }

}