<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_in_category_listing extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_IN_CATEGORY_LISTING_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    public function execute() {
      global $current_category_id, $OSCOM_category;

      $content_width  = MODULE_CONTENT_IN_CATEGORY_LISTING_CONTENT_WIDTH;
      $category_card_layout = MODULE_CONTENT_IN_CATEGORY_LISTING_DISPLAY_ROW;

      $category_name  = $OSCOM_category->getData($current_category_id, 'name');
      $OSCOM_category->setMaximumLevel(1);
      $category_array = $OSCOM_category->buildBranchArray($current_category_id);

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_IN_CATEGORY_LISTING_STATUS' => [
          'title' => 'Enable Sub-Category Listing Module',
          'value' => 'True',
          'desc' => 'Should this module be enabled?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_IN_CATEGORY_LISTING_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_IN_CATEGORY_LISTING_DISPLAY_ROW' => [
          'title' => 'Categories Per Row',
          'value' => 'row row-cols-2 row-cols-sm-3 row-cols-md-4',
          'desc' => 'How many categories should display per Row per viewport?  Default:  XS 2, SM 3, MD and above 4',
        ],
        'MODULE_CONTENT_IN_CATEGORY_LISTING_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '200',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
