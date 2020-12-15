<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2016 osCommerce

  Released under the GNU General Public License
*/

  class ht_category_seo extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_CATEGORY_SEO_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    public function execute() {
      if ( (basename($GLOBALS['PHP_SELF']) === 'index.php') && ($GLOBALS['current_category_id'] > 0) ){
        $category_seo_description = $GLOBALS['OSCOM_category']->get($GLOBALS['current_category_id'], 'seo_description');
        
        if (tep_not_null($category_seo_description)) {
          $GLOBALS['oscTemplate']->addBlock('<meta name="description" content="' . tep_output_string($category_seo_description) . '" />' . PHP_EOL, $this->group);
        }
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_HEADER_TAGS_CATEGORY_SEO_STATUS' => [
          'title' => 'Enable Category Meta Module',
          'value' => 'True',
          'desc' => 'Do you want to allow Category Meta Tags to be added to the page header?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_HEADER_TAGS_CATEGORY_SEO_DESCRIPTION_STATUS' => [
          'title' => 'Display Category Meta Description',
          'value' => 'True',
          'desc' => 'These help your site and your sites visitors.',
          'set_func' => "tep_cfg_select_option(['True'], ",
        ],
        'MODULE_HEADER_TAGS_CATEGORY_SEO_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
  