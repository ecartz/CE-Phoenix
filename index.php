<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

// the following cPath references come from application_top.php
  if (isset($cPath) && tep_not_null($cPath)) {
    $category_depth = (count($category_tree->get_children($current_category_id)) > 0) ? 'nested' : 'products';
  } else {
    $category_depth = 'top';
  }

  require "includes/languages/$language/index.php";

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
