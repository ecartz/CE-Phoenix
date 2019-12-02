<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  class rbsworldpay_hosted {
    var $code, $title, $description, $enabled;

    function __construct() {
      global $order;

      $this->signature = 'rbs|worldpay_hosted|2.0|2.3';
      $this->api_version = '4.6';

      $this->code = 'rbsworldpay_hosted';
      $this->title = MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TEXT_TITLE;
      $this->public_title = MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TEXT_PUBLIC_TITLE;
      $this->description = MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TEXT_DESCRIPTION;
      $this->sort_order = defined('MODULE_PAYMENT_RBSWORLDPAY_HOSTED_SORT_ORDER') ? MODULE_PAYMENT_RBSWORLDPAY_HOSTED_SORT_ORDER : 0;
      $this->enabled = defined('MODULE_PAYMENT_RBSWORLDPAY_HOSTED_STATUS') && (MODULE_PAYMENT_RBSWORLDPAY_HOSTED_STATUS == 'True') ? true : false;
      $this->order_status = defined('MODULE_PAYMENT_RBSWORLDPAY_HOSTED_PREPARE_ORDER_STATUS_ID') && ((int)MODULE_PAYMENT_RBSWORLDPAY_HOSTED_PREPARE_ORDER_STATUS_ID > 0) ? (int)MODULE_PAYMENT_RBSWORLDPAY_HOSTED_PREPARE_ORDER_STATUS_ID : 0;

      if ( defined('MODULE_PAYMENT_RBSWORLDPAY_HOSTED_STATUS') ) {
        if ( MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TESTMODE == 'True' ) {
          $this->title .= ' [Test]';
          $this->public_title .= ' (' . $this->code . '; Test)';
        }

        if ( MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TESTMODE == 'True' ) {
          $this->form_action_url = 'https://secure-test.worldpay.com/wcc/purchase';
        } else {
          $this->form_action_url = 'https://secure.worldpay.com/wcc/purchase';
        }
      }

      if ( $this->enabled === true ) {
        if ( !tep_not_null(MODULE_PAYMENT_RBSWORLDPAY_HOSTED_INSTALLATION_ID) ) {
          $this->description = '<div class="secWarning">' . MODULE_PAYMENT_RBSWORLDPAY_HOSTED_ERROR_ADMIN_CONFIGURATION . '</div>' . $this->description;

          $this->enabled = false;
        }
      }

      if ( $this->enabled === true ) {
        if ( isset($order) && is_object($order) ) {
          $this->update_status();
        }
      }
    }

    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_RBSWORLDPAY_HOSTED_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from zones_to_geo_zones where geo_zone_id = '" . MODULE_PAYMENT_RBSWORLDPAY_HOSTED_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      return false;
    }

    function selection() {
      global $cart_RBS_Worldpay_Hosted_ID;

      if (tep_session_is_registered('cart_RBS_Worldpay_Hosted_ID')) {
        $order_id = substr($cart_RBS_Worldpay_Hosted_ID, strpos($cart_RBS_Worldpay_Hosted_ID, '-')+1);

        $check_query = tep_db_query('select orders_id from orders_status_history where orders_id = "' . (int)$order_id . '" limit 1');

        if (tep_db_num_rows($check_query) < 1) {
          tep_db_query('delete from orders where orders_id = "' . (int)$order_id . '"');
          tep_db_query('delete from orders_total where orders_id = "' . (int)$order_id . '"');
          tep_db_query('delete from orders_status_history where orders_id = "' . (int)$order_id . '"');
          tep_db_query('delete from orders_products where orders_id = "' . (int)$order_id . '"');
          tep_db_query('delete from orders_products_attributes where orders_id = "' . (int)$order_id . '"');
          tep_db_query('delete from orders_products_download where orders_id = "' . (int)$order_id . '"');

          tep_session_unregister('cart_RBS_Worldpay_Hosted_ID');
        }
      }

      return array('id' => $this->code,
                   'module' => $this->public_title);
    }

    function pre_confirmation_check() {
      global $cartID, $cart;

      if (empty($cart->cartID)) {
        $cartID = $cart->cartID = $cart->generate_cart_id();
      }

      if (!tep_session_is_registered('cartID')) {
        tep_session_register('cartID');
      }
    }

    function confirmation() {
      global $cartID, $cart_RBS_Worldpay_Hosted_ID, $customer_id, $languages_id, $order, $order_total_modules;

      $insert_order = false;

      if (tep_session_is_registered('cart_RBS_Worldpay_Hosted_ID')) {
        $order_id = substr($cart_RBS_Worldpay_Hosted_ID, strpos($cart_RBS_Worldpay_Hosted_ID, '-')+1);

        $curr_check = tep_db_query("select currency from orders where orders_id = '" . (int)$order_id . "'");
        $curr = tep_db_fetch_array($curr_check);

        if ( ($curr['currency'] != $order->info['currency']) || ($cartID != substr($cart_RBS_Worldpay_Hosted_ID, 0, strlen($cartID))) ) {
          $check_query = tep_db_query('select orders_id from orders_status_history where orders_id = "' . (int)$order_id . '" limit 1');

          if (tep_db_num_rows($check_query) < 1) {
            tep_delete_order($order_id);
          }

          $insert_order = true;
        }
      } else {
        $insert_order = true;
      }

      if ($insert_order == true) {
        require 'includes/modules/checkout/build_order_totals.php';
        require 'includes/modules/checkout/insert_order.php';

        $cart_RBS_Worldpay_Hosted_ID = $cartID . '-' . $order_id;
        tep_session_register('cart_RBS_Worldpay_Hosted_ID');
      }

      return false;
    }

    function process_button() {
      global $order, $currency, $languages_id, $language, $customer_id, $cart_RBS_Worldpay_Hosted_ID;

      $order_id = substr($cart_RBS_Worldpay_Hosted_ID, strpos($cart_RBS_Worldpay_Hosted_ID, '-')+1);

      $lang_query = tep_db_query("select code from languages where languages_id = '" . (int)$languages_id . "'");
      $lang = tep_db_fetch_array($lang_query);

      $process_button_string = tep_draw_hidden_field('instId', MODULE_PAYMENT_RBSWORLDPAY_HOSTED_INSTALLATION_ID) .
                               tep_draw_hidden_field('cartId', $order_id) .
                               tep_draw_hidden_field('amount', $this->format_raw($order->info['total'])) .
                               tep_draw_hidden_field('currency', $currency) .
                               tep_draw_hidden_field('desc', STORE_NAME) .
                               tep_draw_hidden_field('name', $order->billing['name']) .
                               tep_draw_hidden_field('address1', $order->billing['street_address']) .
                               tep_draw_hidden_field('town', $order->billing['city']) .
                               tep_draw_hidden_field('region', $order->billing['state']) .
                               tep_draw_hidden_field('postcode', $order->billing['postcode']) .
                               tep_draw_hidden_field('country', $order->billing['country']['iso_code_2']) .
                               tep_draw_hidden_field('tel', $order->customer['telephone']) .
                               tep_draw_hidden_field('email', $order->customer['email_address']) .
                               tep_draw_hidden_field('fixContact', 'Y') .
                               tep_draw_hidden_field('hideCurrency', 'true') .
                               tep_draw_hidden_field('lang', strtoupper($lang['code'])) .
                               tep_draw_hidden_field('signatureFields', 'amount:currency:cartId') .
                               tep_draw_hidden_field('signature', md5(MODULE_PAYMENT_RBSWORLDPAY_HOSTED_MD5_PASSWORD . ':' . $this->format_raw($order->info['total']) . ':' . $currency . ':' . $order_id)) .
                               tep_draw_hidden_field('MC_callback', tep_href_link('ext/modules/payment/rbsworldpay/hosted_callback.php', '', 'SSL', false)) .
                               tep_draw_hidden_field('M_sid', tep_session_id()) .
                               tep_draw_hidden_field('M_cid', $customer_id) .
                               tep_draw_hidden_field('M_lang', $language) .
                               tep_draw_hidden_field('M_hash', md5(tep_session_id() . $customer_id . $order_id . $language . number_format($order->info['total'], 2) . MODULE_PAYMENT_RBSWORLDPAY_HOSTED_MD5_PASSWORD));

      if (MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TRANSACTION_METHOD == 'Pre-Authorization') {
        $process_button_string .= tep_draw_hidden_field('authMode', 'E');
      }

      if (MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TESTMODE == 'True') {
        $process_button_string .= tep_draw_hidden_field('testMode', '100');
      }

      return $process_button_string;
    }

    function before_process() {
      global $customer_id, $language, $order, $order_totals, $sendto, $billto, $languages_id, $payment, $currencies, $cart, $cart_RBS_Worldpay_Hosted_ID;
      global $$payment;

      $order_id = substr($cart_RBS_Worldpay_Hosted_ID, strpos($cart_RBS_Worldpay_Hosted_ID, '-')+1);

      if (!isset($_GET['hash']) || ($_GET['hash'] != md5(tep_session_id() . $customer_id . $order_id . $language . number_format($order->info['total'], 2) . MODULE_PAYMENT_RBSWORLDPAY_HOSTED_MD5_PASSWORD))) {
        $this->sendDebugEmail();

        tep_redirect(tep_href_link('shopping_cart.php'));
      }

      $order_query = tep_db_query("select orders_status from orders where orders_id = '" . (int)$order_id . "' and customers_id = '" . (int)$customer_id . "'");

      if (!tep_db_num_rows($order_query)) {
        $this->sendDebugEmail();

        tep_redirect(tep_href_link('shopping_cart.php'));
      }

      $worldpay_order = tep_db_fetch_array($order_query);

      $order_status_id = (MODULE_PAYMENT_RBSWORLDPAY_HOSTED_ORDER_STATUS_ID > 0 ? (int)MODULE_PAYMENT_RBSWORLDPAY_HOSTED_ORDER_STATUS_ID : (int)DEFAULT_ORDERS_STATUS_ID);

      if ($worldpay_order['orders_status'] == MODULE_PAYMENT_RBSWORLDPAY_HOSTED_PREPARE_ORDER_STATUS_ID) {
        tep_db_query("update orders set orders_status = '" . $order_status_id . "', last_modified = now() where orders_id = '" . (int)$order_id . "'");

        $sql_data_array = array('orders_id' => $order_id,
                                'orders_status_id' => $order_status_id,
                                'date_added' => 'now()',
                                'customer_notified' => (SEND_EMAILS == 'true') ? '1' : '0',
                                'comments' => $order->info['comments']);

        tep_db_perform('orders_status_history', $sql_data_array);
      } else {
        $order_status_query = tep_db_query("select orders_status_history_id from orders_status_history where orders_id = '" . (int)$order_id . "' and orders_status_id = '" . (int)$order_status_id . "' and comments = '' order by date_added desc limit 1");

        if (tep_db_num_rows($order_status_query)) {
          $order_status = tep_db_fetch_array($order_status_query);

          $sql_data_array = array('customer_notified' => (SEND_EMAILS == 'true') ? '1' : '0',
                                  'comments' => $order->info['comments']);

          tep_db_perform('orders_status_history', $sql_data_array, 'update', "orders_status_history_id = '" . (int)$order_status['orders_status_history_id'] . "'");
        }
      }

      $trans_result = 'WorldPay: Transaction Verified';

      if (MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TESTMODE == 'True') {
        $trans_result .= "\n" . MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TEXT_WARNING_DEMO_MODE;
      }

      $sql_data_array = array('orders_id' => $order_id,
                              'orders_status_id' => MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TRANSACTIONS_ORDER_STATUS_ID,
                              'date_added' => 'now()',
                              'customer_notified' => '0',
                              'comments' => $trans_result);

      tep_db_perform('orders_status_history', $sql_data_array);

      tep_notify('checkout', $order);

// load the after_process function from the payment modules
      $this->after_process();

      require 'includes/modules/checkout/reset.php';

      tep_session_unregister('cart_RBS_Worldpay_Hosted_ID');

      tep_redirect(tep_href_link('checkout_success.php', '', 'SSL'));
    }

    function after_process() {
      return false;
    }

    function get_error() {
      return false;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from configuration where configuration_key = 'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install($parameter = null) {
      $params = $this->getParams();

      if (isset($parameter)) {
        if (isset($params[$parameter])) {
          $params = array($parameter => $params[$parameter]);
        } else {
          $params = array();
        }
      }

      foreach ($params as $key => $data) {
        $sql_data_array = array('configuration_title' => $data['title'],
                                'configuration_key' => $key,
                                'configuration_value' => (isset($data['value']) ? $data['value'] : ''),
                                'configuration_description' => $data['desc'],
                                'configuration_group_id' => '6',
                                'sort_order' => '0',
                                'date_added' => 'now()');

        if (isset($data['set_func'])) {
          $sql_data_array['set_function'] = $data['set_func'];
        }

        if (isset($data['use_func'])) {
          $sql_data_array['use_function'] = $data['use_func'];
        }

        tep_db_perform('configuration', $sql_data_array);
      }
    }

    function remove() {
      tep_db_query("delete from configuration where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      $keys = array_keys($this->getParams());

      if ($this->check()) {
        foreach ($keys as $key) {
          if (!defined($key)) {
            $this->install($key);
          }
        }
      }

      return $keys;
    }

    function getParams() {
      if (!defined('MODULE_PAYMENT_RBSWORLDPAY_HOSTED_PREPARE_ORDER_STATUS_ID')) {
        $check_query = tep_db_query("select orders_status_id from orders_status where orders_status_name = 'Preparing [WorldPay]' limit 1");

        if (tep_db_num_rows($check_query) < 1) {
          $status_query = tep_db_query("select max(orders_status_id) as status_id from orders_status");
          $status = tep_db_fetch_array($status_query);

          $status_id = $status['status_id']+1;

          $languages = tep_get_languages();

          foreach ($languages as $lang) {
            tep_db_query("insert into orders_status (orders_status_id, language_id, orders_status_name) values ('" . $status_id . "', '" . $lang['id'] . "', 'Preparing [WorldPay]')");
          }

          $flags_query = tep_db_query("describe orders_status public_flag");
          if (tep_db_num_rows($flags_query) == 1) {
            tep_db_query("update orders_status set public_flag = 0 and downloads_flag = 0 where orders_status_id = '" . $status_id . "'");
          }
        } else {
          $check = tep_db_fetch_array($check_query);

          $status_id = $check['orders_status_id'];
        }
      } else {
        $status_id = MODULE_PAYMENT_RBSWORLDPAY_HOSTED_PREPARE_ORDER_STATUS_ID;
      }

      if (!defined('MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TRANSACTIONS_ORDER_STATUS_ID')) {
        $check_query = tep_db_query("select orders_status_id from orders_status where orders_status_name = 'WorldPay [Transactions]' limit 1");

        if (tep_db_num_rows($check_query) < 1) {
          $status_query = tep_db_query("select max(orders_status_id) as status_id from orders_status");
          $status = tep_db_fetch_array($status_query);

          $tx_status_id = $status['status_id']+1;

          $languages = tep_get_languages();

          foreach ($languages as $lang) {
            tep_db_query("insert into orders_status (orders_status_id, language_id, orders_status_name) values ('" . $tx_status_id . "', '" . $lang['id'] . "', 'WorldPay [Transactions]')");
          }

          $flags_query = tep_db_query("describe orders_status public_flag");
          if (tep_db_num_rows($flags_query) == 1) {
            tep_db_query("update orders_status set public_flag = 0 and downloads_flag = 0 where orders_status_id = '" . $tx_status_id . "'");
          }
        } else {
          $check = tep_db_fetch_array($check_query);

          $tx_status_id = $check['orders_status_id'];
        }
      } else {
        $tx_status_id = MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TRANSACTIONS_ORDER_STATUS_ID;
      }

      $params = array('MODULE_PAYMENT_RBSWORLDPAY_HOSTED_STATUS' => array('title' => 'Enable WorldPay Hosted Payment Pages',
                                                                          'desc' => 'Do you want to accept WorldPay Hosted Payment Pages payments?',
                                                                          'value' => 'True',
                                                                          'set_func' => 'tep_cfg_select_option(array(\'True\', \'False\'), '),
                      'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_INSTALLATION_ID' => array('title' => 'Installation ID',
                                                                                   'desc' => 'The WorldPay Account Installation ID to accept payments for'),
                      'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_CALLBACK_PASSWORD' => array('title' => 'Callback Password',
                                                                                     'desc' => 'The password sent to the callback processing script. This must be the same value defined in the WorldPay Merchant Interface.'),
                      'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_MD5_PASSWORD' => array('title' => 'MD5 Password',
                                                                                'desc' => 'The MD5 password to verify transactions with. This must be the same value defined in the WorldPay Merchant Interface.'),
                      'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TRANSACTION_METHOD' => array('title' => 'Transaction Method',
                                                                                      'desc' => 'The processing method to use for each transaction.',
                                                                                      'value' => 'Capture',
                                                                                      'set_func' => 'tep_cfg_select_option(array(\'Pre-Authorization\', \'Capture\'), '),
                      'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_PREPARE_ORDER_STATUS_ID' => array('title' => 'Set Preparing Order Status',
                                                                                           'desc' => 'Set the status of prepared orders made with this payment module to this value',
                                                                                           'value' => $status_id,
                                                                                           'set_func' => 'tep_cfg_pull_down_order_statuses(',
                                                                                           'use_func' => 'tep_get_order_status_name'),
                      'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_ORDER_STATUS_ID' => array('title' => 'Set Order Status',
                                                                                   'desc' => 'Set the status of orders made with this payment module to this value',
                                                                                   'value' => '0',
                                                                                   'set_func' => 'tep_cfg_pull_down_order_statuses(',
                                                                                   'use_func' => 'tep_get_order_status_name'),
                      'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TRANSACTIONS_ORDER_STATUS_ID' => array('title' => 'Transactions Order Status Level',
                                                                                                'desc' => 'Include WorldPay transaction information in this order status level.',
                                                                                                'value' => $tx_status_id,
                                                                                                'use_func' => 'tep_get_order_status_name',
                                                                                                'set_func' => 'tep_cfg_pull_down_order_statuses('),
                      'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_ZONE' => array('title' => 'Payment Zone',
                                                                        'desc' => 'If a zone is selected, only enable this payment method for that zone.',
                                                                        'value' => '0',
                                                                        'use_func' => 'tep_get_zone_class_title',
                                                                        'set_func' => 'tep_cfg_pull_down_zone_classes('),
                      'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_TESTMODE' => array('title' => 'Test Mode',
                                                                            'desc' => 'Should transactions be processed in test mode?',
                                                                            'value' => 'False',
                                                                            'set_func' => 'tep_cfg_select_option(array(\'True\', \'False\'), '),
                      'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_DEBUG_EMAIL' => array('title' => 'Debug E-Mail Address',
                                                                               'desc' => 'All parameters of an invalid transaction will be sent to this email address if one is entered.'),
                      'MODULE_PAYMENT_RBSWORLDPAY_HOSTED_SORT_ORDER' => array('title' => 'Sort order of display.',
                                                                              'desc' => 'Sort order of display. Lowest is displayed first.',
                                                                              'value' => '0'));

      return $params;
    }

// format prices without currency formatting
    function format_raw($number, $currency_code = '', $currency_value = '') {
      global $currencies, $currency;

      if (empty($currency_code) || !$this->is_set($currency_code)) {
        $currency_code = $currency;
      }

      if (empty($currency_value) || !is_numeric($currency_value)) {
        $currency_value = $currencies->currencies[$currency_code]['value'];
      }

      return number_format(tep_round($number * $currency_value, $currencies->currencies[$currency_code]['decimal_places']), $currencies->currencies[$currency_code]['decimal_places'], '.', '');
    }

    function sendDebugEmail($response = array()) {
      if (tep_not_null(MODULE_PAYMENT_RBSWORLDPAY_HOSTED_DEBUG_EMAIL)) {
        $email_body = '';

        if (!empty($response)) {
          $email_body .= 'RESPONSE:' . "\n\n" . print_r($response, true) . "\n\n";
        }

        if (!empty($_POST)) {
          $email_body .= '$_POST:' . "\n\n" . print_r($_POST, true) . "\n\n";
        }

        if (!empty($_GET)) {
          $email_body .= '$_GET:' . "\n\n" . print_r($_GET, true) . "\n\n";
        }

        if (!empty($email_body)) {
          tep_mail('', MODULE_PAYMENT_RBSWORLDPAY_HOSTED_DEBUG_EMAIL, 'WorldPay Hosted Debug E-Mail', trim($email_body), STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);
        }
      }
    }
  }
?>
