<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  if (!isset($_GET['products_id'])) {
    tep_redirect(tep_href_link('index.php'));
  }

  require language::map_to_translation('product_info.php');

  if ($product->get('status')) {
    $product->increment_view_count();

    require $oscTemplate->map_to_template(__FILE__, 'page');
  } else {
    require $oscTemplate->map_to_template('product_info_not_found.php', 'page');
  }

  require 'includes/application_bottom.php';
