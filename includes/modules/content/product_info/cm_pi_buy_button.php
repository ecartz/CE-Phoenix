<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_pi_buy_button extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PI_BUY_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $product;

      $data_attributes = 'data-has-attributes="' . (int)$product->get('has_attributes')
                       . '" data-in-stock="' . (int)$product->get('in_stock')
                       . '" data-product-id="' . (int)$product->get('id') . '"';

      $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_PI_BUY_STATUS' => [
          'title' => 'Enable Buy Button',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_BUY_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '6',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_BUY_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '100',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
