<?php
/*
	Plugin Name: Онлайн касса КОМТЕТ Касса
	Description: Плагин для отправки электронных чеков и фискализации по 54-ФЗ
	Author: КОМТЕТ Касса
	Version: 1.0.0
*/

require PLUGIN_DIR.'komtet-kassa/lib/komtet-kassa-php-sdk/autoload.php';

use Komtet\KassaSdk\CalculationMethod;

new KomtetKassa;

class KomtetKassa{

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
         }

        return true;
    }

    static function updateOrder($args) {
        var_dump($args);
        die();
    }

    static function buildCheck($orderId, $mogutaOrder, $paymentType, $checkType) {

        return true;
    }

    static function fiscalizeOrder($orderId, $mogutaOrder, $check, $checkType) {

        return true;
    }

	static function convertToRub($rates, $price, $currency) {
		$iso = false;
		if (array_key_exists('RUB', $rates)) {
			$iso = 'RUB';
		}
		if (array_key_exists('RUR', $rates)) {
			$iso = 'RUR';
		}
		if ($iso && array_key_exists($currency, $rates)) {
			return (float)round($price*$rates[$currency]/$rates[$iso],2);
		}
 		return $price;
	}

	static function getToken($ignoreTimer = false) {
		if (!self::$options) {
			self::$options = unserialize(stripslashes(MG::getSetting('atolOption')));
			if (!self::$options['apiVers']) {
				self::$options['apiVers'] = 'v4';
			}
		}

		$time = time();

		if (isset(self::$options['tokenValidTime']) && isset(self::$options['token']) && self::$options['tokenValidTime'] > $time && !$ignoreTimer) {
			return self::$options['token'];
		}

		if (self::$options['testMode']=='true') {
			$url = 'https://testonline.atol.ru/possystem/'.self::$options['apiVers'].'/getToken';
		} else {
			$url = 'https://online.atol.ru/possystem/'.self::$options['apiVers'].'/getToken';
		}

		$ch = curl_init($url);

		$jsonData = array(
			'login' => self::$options['atolLogin'],
			'pass' => self::$options['atolPass']
		);

		$jsonDataEncoded = json_encode($jsonData);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8'));

		$result = curl_exec($ch);
		$result = json_decode($result, true);

		// mg::loger($url);
		// mg::loger($result);

		if ($result['token']) {
			self::$options['token'] = $result['token'];
			self::$options['tokenValidTime'] = $time + self::$tokenTimer;
			MG::setOption(array('option' => 'atolOption', 'value' => addslashes(serialize(self::$options))));
			return $result['token'];
		} else {
			mg::loger('[плагин Atol] Ошибка при получении токена');
			mg::loger($url);
			mg::loger($jsonData);
			mg::loger($result);
			return $result['error'];
		}
	}

	static function orderPayPublic($args){//функция хука
		if (!isset($args['args']['paymentID']) || !isset($args['args']['paymentOrderId'])) {
			return $args['result'];
		}

		self::$options = unserialize(stripslashes(MG::getSetting('atolOption')));
		if (empty(self::$options['apiVers'])) {
			self::$options['apiVers'] = 'v4';
		}
		if (empty(self::$options['prefix'])) {
			self::$options['prefix'] = '';
		}

		if (in_array($args['args']['paymentID'], array(3, 4, 7, 12, 13)) || $args['args']['paymentID'] > 999 || in_array($args['args']['paymentID'], self::$options['paymentDisable'])) {
				return $args['result'];
		}

		$token = self::getToken();

		if (is_array($token)) {

			DB::query("INSERT IGNORE INTO `".PREFIX."atol`
					(`id`, `uuid`, `status`, `fn_number`) VALUES
					(".DB::quote($args['args']['paymentOrderId']).", '', 'error', ".DB::quote($token['text']).")
					");

			return $args['result'];
		}

		$order = DB::query("SELECT `id`, `user_email`, `order_content`, `phone`, `currency_iso`, `number`, `delivery_cost`, `yur_info`
								FROM `".PREFIX."order` WHERE `id` = ".$args['args']['paymentOrderId']);

		$rates = MG::getSetting('dbCurrRates');
		if (empty($rates)) {
			$rates = MG::getSetting('currencyRate');
		}

		$order = DB::fetchAssoc($order);
		$order['order_content'] = unserialize(stripslashes($order['order_content']));
		$order['phone'] = preg_replace("/[^0-9+]/", '', $order['phone']);

		$jsonData = array(
			"timestamp" => date("d.m.Y H:i:s"),
			"external_id" => self::$options['prefix'].$order['id'],
			"service" => array(
				"callback_url" => PROTOCOL."://".$_SERVER['SERVER_NAME'].'/atolresponse',
			),
			"receipt" => array(
				"client" => array(
					"email" => $order['user_email']
				),
				"company" => array(
					"sno" => self::$options['sno'],
					"email" => self::$options['seller_email']?self::$options['seller_email']:MG::getSetting('adminEmail'),
					"inn" => self::$options['inn'],
					"payment_address" => self::$options['payment_address']
				),
				"items" => array(),
				"payments" => array(),
				"vats" => array(),
			)
		);

		if ($order['phone']) {
			$jsonData['receipt']['client']['phone'] = $order['phone'];
		} else {
			unset($jsonData['receipt']['client']['phone']);
		}

		foreach ($order['order_content'] as $prod) {
			$price = self::convertToRub($rates, (float)$prod['price'], $order['currency_iso']);

			$tmp = explode(PHP_EOL, $prod['name']);

			$item = array(
				"price" => $price,
				"quantity" => (float)$prod['count'],
				"name" => MG::textMore($tmp[0], 125),
				"sum" => (float)round($price * $prod['count'], 2),
				"payment_method" => 'full_prepayment',
				"vat" => array('type' => self::$options['tax']),
				"payment_object" => 'commodity',
			);

			$jsonData["receipt"]["items"][] = $item;
			unset($item);
			unset($tmp);
			unset($price);
		}

		if ((float)$order['delivery_cost'] > 0) {
			$price = $order['delivery_cost'];
			$price = self::convertToRub($rates, $price, $order['currency_iso']);
			$item = array(
				"price" => round($price,2),
				"quantity" => 1,
				"name" => 'Доставка',
				"sum" => round($price,2),
				"payment_method" => 'full_prepayment',
				"vat" => array('type' => self::$options['tax']),
				"payment_object" => 'service',
			);

			$jsonData["receipt"]["items"][] = $item;
			unset($item);
			unset($tmp);
			unset($price);
		}

		$total = 0;
		foreach ($jsonData["receipt"]["items"] as $key => $value) {
			$total+= $value["sum"];
		}
		$jsonData["receipt"]["total"] = $total;
		$jsonData["receipt"]["payments"][] = array("sum" => $total, "type" => 1);
		$jsonData["receipt"]["vats"][] = array("type" => self::$options['tax']);

		if ($order['yur_info']) {
			$order['yur_info'] = unserialize(stripcslashes($order['yur_info']));
			if (!empty($order['yur_info']['nameyur']) && !empty($order['yur_info']['inn'])) {
				$jsonData['receipt']['client']['name'] = $order['yur_info']['nameyur'];
				$jsonData['receipt']['client']['inn'] = $order['yur_info']['inn'];
			}
		}

		if (self::$options['testMode']=='true') {
			$url = 'https://testonline.atol.ru/possystem/'.self::$options['apiVers'].'/'.self::$options['atolGroup'].'/sell';
		} else {
			$url = 'https://online.atol.ru/possystem/'.self::$options['apiVers'].'/'.self::$options['atolGroup'].'/sell';
		}

		// mg::loger('atol arr');
		// mg::loger($jsonData);

		$headers = array('Content-Type: application/json; charset=utf-8');
		$headers[] = 'Token:'.$token;

		$ch = curl_init($url);
		$jsonDataEncoded = json_encode($jsonData);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$result = curl_exec($ch);
		$result = json_decode($result, true);
		unset($ch);

		// mg::loger($url);
		// MG::loger($jsonData);
		// MG::loger($result);

		if ($result['status'] == 'wait' && empty($result['error'])) {
			DB::query("INSERT IGNORE INTO `".PREFIX."atol`
				(`id`, `name`, `uuid`, `status`, `fn_number`, `request`) VALUES
				(".DB::quote($order['id']).", ".DB::quote($order['number']).", ".DB::quote($result['uuid']).", ".DB::quote($result['status']).", '', ".DB::quote($jsonDataEncoded).")");
		} else {
			DB::query("INSERT IGNORE INTO `".PREFIX."atol`
				(`id`, `name`, `uuid`, `status`, `fn_number`) VALUES
				(".DB::quote($order['id']).", ".DB::quote($order['number']).", ".DB::quote($result['error']['error_id']).", 'error', ".DB::quote($result['error']['text']).")");
		}
		return $args['result'];
	}

	static function atolResponse($arg) {
		if(URL::isSection('atolresponse')) {
			$result = json_decode(file_get_contents('php://input'), true);

			if ($result['uuid']) {
				if ($result['status'] == 'done') {
					DB::query("UPDATE `".PREFIX."atol`
						SET `status` = ".DB::quote($result['status']).",
							`fn_number` = ".DB::quote($result['payload']['fiscal_document_attribute'])."
						WHERE `uuid` = ".DB::quote($result['uuid']));
				} else {
					DB::query("UPDATE `".PREFIX."atol`
						SET `status` = ".DB::quote($result['status']).",
							`fn_number` = ".DB::quote($result['error']['text'])."
						WHERE `uuid` = ".DB::quote($result['uuid']));
				}
			}
		} elseif (URL::isSection('atolrefund')) {
			$result = json_decode(file_get_contents('php://input'), true);
			if ($result['uuid']) {
				if ($result['status'] == 'done') {
					DB::query("UPDATE `".PREFIX."atol`
						SET `status` = 'done_refund',
							`fn_number` = ".DB::quote($result['payload']['fiscal_document_attribute'])."
						WHERE `uuid` = ".DB::quote($result['uuid']));
				} else {
mg::loger('-------------------------atol refund result-----------------------------');
mg::loger($result);
					DB::query("UPDATE `".PREFIX."atol`
						SET `status` = ".DB::quote($result['status'].'_refund').",
							`fn_number` = ".DB::quote($result['error']['text'])."
						WHERE `uuid` = ".DB::quote($result['uuid']));
				}
			}
		}
		return $arg['result'];
	}

	static function refund($id) {
		$res = DB::query("SELECT `request` FROM `".PREFIX."atol` WHERE `id` = ".DB::quoteInt($id));
		if ($row = DB::fetchAssoc($res)) {
			$jsonData = json_decode($row['request'], 1);
			$jsonData['timestamp'] = date("d.m.Y H:i:s");
			$jsonData['external_id'] .= '_refund';
			$jsonData['service']['callback_url'] =	PROTOCOL."://".$_SERVER['SERVER_NAME'].'/atolrefund';

			self::$options = unserialize(stripslashes(MG::getSetting('atolOption')));
			if (empty(self::$options['apiVers'])) {
				self::$options['apiVers'] = 'v4';
			}
			if (empty(self::$options['prefix'])) {
				self::$options['prefix'] = '';
			}

			$token = self::getToken();
			if (is_array($token)) {
				return $token['text'];
			}

			if (self::$options['testMode']=='true') {
				$url = 'https://testonline.atol.ru/possystem/'.self::$options['apiVers'].'/'.self::$options['atolGroup'].'/sell_refund';
			} else {
				$url = 'https://online.atol.ru/possystem/'.self::$options['apiVers'].'/'.self::$options['atolGroup'].'/sell_refund';
			}

			$headers = array('Content-Type: application/json; charset=utf-8');
			$headers[] = 'Token:'.$token;

			$ch = curl_init($url);
			$jsonDataEncoded = json_encode($jsonData);
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonDataEncoded);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			$result = curl_exec($ch);
			$result = json_decode($result, true);
			unset($ch);

			// mg::loger($url);
			// MG::loger($jsonData);
			// MG::loger($result);

			if ($result['status'] == 'wait' && empty($result['error'])) {
				DB::query("UPDATE `".PREFIX."atol` SET
					`uuid` = ".DB::quote($result['uuid']).",
					`status` = ".DB::quote($result['status']).",
					`fn_number` = ''
					WHERE `id` = ".DB::quoteInt($id));

				return true;
			}

			return !empty($result['error']['text'])?$result['error']['text']:'Не удалось получить ответ от сервиса atol';
		}
		return 'Заказ не найден';
	}
}