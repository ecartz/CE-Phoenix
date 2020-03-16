<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  $OSCOM_Hooks->register('progress');

// if the customer is not logged on, redirect them to the login page
  if (!isset($_SESSION['customer_id'])) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link('login.php', '', 'SSL'));
  }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

  if (!$customer_data->has('address')) {
    tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
  }

  // needs to be included earlier to set the success message in the messageStack
  require "includes/languages/$language/checkout_payment_address.php";

  $message_stack_area = 'checkout_address';

  $error = false;
  $process = false;
  if (tep_validate_form_action_is('submit')) {
    $customer_details = $customer_data->process($customer_data->get_fields_for_page('address_book'));
    $OSCOM_Hooks->call('siteWide', 'injectFormVerify');
    if ($customer_details) {
      $customer_details['id'] = $customer->get_id();
      $customer_data->add_address($customer_details);

      $_SESSION['billto'] = $customer_data->get('address_book_id', $customer_details);

      unset($_SESSION['payment']);

      tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
    } elseif (isset($_POST['address'])) {
      // process the selected billing destination
      $reset_payment = isset($_SESSION['billto']) && ($_SESSION['billto'] != $_POST['address']) && isset($_SESSION['payment']);
      $_SESSION['billto'] = $_POST['address'];

      if ($customer->fetch_to_address($_SESSION['billto'])) {
        if ($reset_payment) {
          unset($_SESSION['payment']);
        }
        tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
      } else {
        unset($_SESSION['billto']);
      }
    } else {
      // no addresses to select from - customer decided to keep the current assigned address
      $_SESSION['billto'] = $customer->get_default_address_id();

      tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
    }
  }

// if no billing destination address was selected, use their own address as default
  if (!isset($_SESSION['billto'])) {
    $_SESSION['billto'] = $customer->get_default_address_id();
    $billto =& $_SESSION['billto'];
  }

  $addresses_count = $customer->count_addresses();

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
