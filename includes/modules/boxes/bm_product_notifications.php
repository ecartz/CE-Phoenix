<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class bm_product_notifications extends abstract_block_module {

    const CONFIG_KEY_BASE = 'MODULE_BOXES_PRODUCT_NOTIFICATIONS_';

    protected function has_notification() {
      if (!isset($_SESSION['customer_id'])) {
        return false;
      }

      $check_query = tep_db_query("SELECT COUNT(*) AS `count` FROM products_notifications WHERE products_id = " . (int)$_GET['products_id'] . " AND customers_id = " . (int)$_SESSION['customer_id']);
      $check = tep_db_fetch_array($check_query);

      return ($check['count'] > 0);
    }

    public function execute() {
      if (isset($_GET['products_id'])) {
        $notification = [
          'link' => tep_href_link($GLOBALS['PHP_SELF'], tep_get_all_get_params(['action']) . 'action=' . ($this->has_notification() ? 'notify_remove' : 'notify')),
          'message' => sprintf(
            $notification_exists ? MODULE_BOXES_PRODUCT_NOTIFICATIONS_BOX_NOTIFY_REMOVE : MODULE_BOXES_PRODUCT_NOTIFICATIONS_BOX_NOTIFY,
            $GLOBALS['product']->get('name')),
        ];

        $tpl_data = ['group' => $this->group, 'file' => __FILE__];
        include 'includes/modules/block_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_BOXES_PRODUCT_NOTIFICATIONS_STATUS' => [
          'title' => 'Enable Product Notifications Module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_BOXES_PRODUCT_NOTIFICATIONS_CONTENT_PLACEMENT' => [
          'title' => 'Content Placement',
          'value' => 'Right Column',
          'desc' => 'Should the module be loaded in the left or right column?',
          'set_func' => "tep_cfg_select_option(['Left Column', 'Right Column'], ",
        ],
        'MODULE_BOXES_PRODUCT_NOTIFICATIONS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }

