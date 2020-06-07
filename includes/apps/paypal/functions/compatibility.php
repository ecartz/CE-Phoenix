<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

foreach ([
  'FILENAME_ACCOUNT_HISTORY_INFO' => 'order_info.php',
  'FILENAME_CHECKOUT_CONFIRMATION' => 'checkout_confirmation.php',
  'FILENAME_CHECKOUT_PAYMENT' => 'checkout_payment.php',
  'FILENAME_CHECKOUT_PROCESS' => 'checkout_process.php',
  'FILENAME_CHECKOUT_SHIPPING' => 'checkout_shipping.php',
  'FILENAME_CHECKOUT_SHIPPING_ADDRESS' => 'checkout_shipping_address.php',
  'FILENAME_CHECKOUT_SUCCESS' => 'checkout_success.php',
  'FILENAME_CREATE_ACCOUNT' => 'register.php',
  'FILENAME_DEFAULT' => 'index.php',
  'FILENAME_LOGIN' => 'login.php',
  'FILENAME_ORDERS' => 'orders.php',
  'FILENAME_PRODUCT_INFO' => 'product_info.php',
  'FILENAME_SHOPPING_CART' => 'shopping_cart.php',
] as $key => $value) {
  if (!defined($key)) {
    define($key, $value);
  }
}
