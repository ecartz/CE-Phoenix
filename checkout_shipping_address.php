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

// if there is nothing in the customer's cart, redirect to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link('shopping_cart.php'));
  }

  // needs to be included earlier to set the success message in the messageStack
  require "includes/languages/$language/checkout_shipping_address.php";

  $order = new order();

// if the order contains only virtual products, forward the customer to the billing page as
// a shipping address is not needed
  if ($order->content_type == 'virtual') {
    $_SESSION['shipping'] = false;
    $_SESSION['sendto'] = false;
    tep_redirect(tep_href_link('checkout_payment.php', '', 'SSL'));
  }

  $message_stack_area = 'checkout_address';

  $error = false;
  $process = false;
  if (tep_validate_form_action_is('submit')) {
// process a new shipping address
    $customer_details = $customer_data->process($customer_data->get_fields_for_page('address_book'));
    $OSCOM_Hooks->call('siteWide', 'injectFormVerify');
    if ($customer_details) {
      $customer_details['id'] = $customer->get_id();
      $customer_data->add_address($customer_details);

      $_SESSION['sendto'] = $customer_data->get('address_book_id', $customer_details);

      unset($_SESSION['shipping']);

      tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
    } elseif (isset($_POST['address'])) {
// change to the selected shipping destination
      $reset_shipping = (isset($_SESSION['sendto']) && ($_SESSION['sendto'] != $_POST['address']) && isset($_SESSION['shipping']));
      $_SESSION['sendto'] = $_POST['address'];

      if ($customer->fetch_to_address((int)$_SESSION['sendto'])) {
        if ($reset_shipping) {
          unset($_SESSION['shipping']);
        }

        tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
      } else {
        unset($_SESSION['sendto']);
      }
    } else {
      $_SESSION['sendto'] = $customer->get_default_address_id();

      tep_redirect(tep_href_link('checkout_shipping.php', '', 'SSL'));
    }
  }

// if no shipping destination address was selected, use their own address as default
  if (!isset($_SESSION['sendto'])) {
    $_SESSION['sendto'] = $customer->get_default_address_id();
    $sendto =& $_SESSION['sendto'];
  }

  $addresses_count = $customer->count_addresses();

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
