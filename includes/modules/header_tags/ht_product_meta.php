<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class ht_product_meta extends abstract_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_PRODUCT_META_';

    protected $group = 'header_tags';

    function execute() {
      global $product;

      if (isset($_GET['products_id'], $product) && tep_not_null($product->get('seo_description'))) {
        $GLOBALS['oscTemplate']->addBlock('<meta name="description" content="' . tep_output_string($product->get('seo_description')) . '" />' . PHP_EOL, $this->group);
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_HEADER_TAGS_PRODUCT_META_STATUS' => [
          'title' => 'Enable Product Meta Module',
          'value' => 'True',
          'desc' => 'Do you want to allow product meta tags to be added to the page header?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_HEADER_TAGS_PRODUCT_META_KEYWORDS_STATUS' => [
          'title' => 'Enable Keyword Search Engine',
          'value' => 'True',
          'desc' => 'Keywords can be used as an internal search engine...',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_HEADER_TAGS_PRODUCT_META_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
