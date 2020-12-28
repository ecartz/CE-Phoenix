<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class osC_Actions_add_product {

    public static function execute() {
      if (isset($_POST['products_id'])) {
        $pid = (int)$_POST['products_id'];
        $attributes = $_POST['id'] ?? null;

        $qty = empty($_POST['qty']) ? 1 : (int)$_POST['qty'];

        $name = $_SESSION['cart']->add_cart($_POST['products_id'], $_SESSION['cart']->get_quantity(Product::build_uprid($pid, $attributes))+$qty, $attributes);

        if ($name) {
          $GLOBALS['messageStack']->add_session('product_action', sprintf(PRODUCT_ADDED, $name), 'success');
        }
      }

      tep_redirect(tep_href_link($GLOBALS['goto'], tep_get_all_get_params($GLOBALS['parameters'])));
    }

  }
