<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  chdir('../../../../');
  require 'includes/application_top.php';

  $hooks->register_pipeline('loginRequired');

  if (!isset($_GET['products_id'])) {
    tep_redirect(tep_href_link('index.php'));
  }

  require "includes/languages/$language/modules/content/reviews/write.php";

  $reviewed = [];
  $reviewed_products_query = tep_db_query("SELECT DISTINCT products_id FROM reviews WHERE customers_id = " . (int)$_SESSION['customer_id']);
  while ($reviewed_products = tep_db_fetch_array($reviewed_products_query)) {
    $reviewed[] = $reviewed_products['products_id'];
  }

  if (in_array((int)$_GET['products_id'], $reviewed)) {
    $messageStack->add_session('product_action', sprintf(TEXT_ALREADY_REVIEWED, $customer->get_short_name()), 'error');

    tep_redirect(tep_href_link('product_info.php', tep_get_all_get_params(['action'])));
  }

  if (ALLOW_ALL_REVIEWS == 'false') {
    $purchased = [];
    $purchased_products_array = tep_db_query("SELECT DISTINCT op.products_id FROM orders o, orders_products op WHERE o.customers_id = " . (int)$_SESSION['customer_id'] . " AND o.orders_id = op.orders_id GROUP BY products_id");

    while ($purchased_products = tep_db_fetch_array($purchased_products_array)) {
      $purchased[] = $purchased_products['products_id'];
    }

    $allowable_reviews = array_diff($purchased, $reviewed);

    if (!in_array((int)$_GET['products_id'], $allowable_reviews)) {
      $messageStack->add_session('product_action', sprintf(TEXT_NOT_PURCHASED, $customer->get_short_name()), 'error');

      tep_redirect(tep_href_link('product_info.php', tep_get_all_get_params(['action'])));
    }
  }

  $product = new product_by_id($_GET['products_id']);

  if (!$product->get('status')) {
    tep_redirect(tep_href_link('product_info.php', 'products_id=' . (int)$_GET['products_id']));
  }

  if (tep_validate_form_action_is('process')) {
    $rating = tep_db_prepare_input($_POST['rating']);
    $review = tep_db_prepare_input($_POST['review']);
    $nickname = tep_db_prepare_input($_POST['nickname']);

    if ((ALLOW_ALL_REVIEWS == 'false') && ($_POST['nickname'] != $customer->get_short_name())) {
      $nickname = sprintf(VERIFIED_BUYER, $nickname);
    }

    tep_db_query("INSERT INTO reviews (products_id, customers_id, customers_name, reviews_rating, date_added) VALUES ('" . (int)$_GET['products_id'] . "', '" . (int)$_SESSION['customer_id'] . "', '" . tep_db_input($nickname) . "', '" . tep_db_input($rating) . "', NOW())");
    $insert_id = tep_db_insert_id();

    tep_db_query("INSERT INTO reviews_description (reviews_id, languages_id, reviews_text) VALUES ('" . (int)$insert_id . "', '" . (int)$_SESSION['languages_id'] . "', '" . tep_db_input($review) . "')");

    $OSCOM_Hooks->call('write', 'addNewAction');

    $messageStack->add_session('product_action', sprintf(TEXT_REVIEW_RECEIVED, $nickname), 'success');

    tep_redirect(tep_href_link('product_info.php', tep_get_all_get_params(['action'])));
  }

  require $oscTemplate->map_to_template(__FILE__, 'ext');
  require 'includes/application_bottom.php';
