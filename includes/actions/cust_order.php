<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class osC_Actions_cust_order {

    public static function execute() {
      if (isset($_SESSION['customer_id'], $_GET['pid'])) {
        $pid = (int)$_GET['pid'];
        $product = product_by_id::build($pid);

        if ($product->get('has_attributes')) {
          tep_redirect($product->get('link'));
        } else {
          $_SESSION['cart']->add_cart($product, $_SESSION['cart']->get_quantity($pid)+1);

          $GLOBALS['messageStack']->add_session('product_action', sprintf(PRODUCT_ADDED, $product->get('name')), 'success');
        }
      }

      tep_redirect(tep_href_link($GLOBALS['goto'], tep_get_all_get_params($GLOBALS['parameters'])));
    }

  }
