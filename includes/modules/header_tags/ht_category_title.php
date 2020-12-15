<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class ht_category_title extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_HEADER_TAGS_CATEGORY_TITLE_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    public function build_title($current_category_id) {
      if ( ('True' === MODULE_HEADER_TAGS_CATEGORY_TITLE_SEO_TITLE_OVERRIDE)
        && tep_not_null($category_seo_title = $GLOBALS['OSCOM_category']->get($current_category_id, 'seo_title')))
      {
        return $category_seo_title;
      }

      return $GLOBALS['OSCOM_category']->get($current_category_id, 'name');
    }

    public function execute() {
      if ( (basename($GLOBALS['PHP_SELF']) == 'index.php') && ($GLOBALS['current_category_id'] > 0) ) {
        $GLOBALS['oscTemplate']->setTitle(
          $this->build_title($GLOBALS['current_category_id'])
        . MODULE_HEADER_TAGS_CATEGORY_SEO_SEPARATOR
        . $GLOBALS['oscTemplate']->getTitle());
      }
    }

    protected function get_parameters() {
      return [
        $this->config_key_base . 'STATUS' => [
          'title' => 'Enable Category Title Module',
          'value' => 'True',
          'desc' => 'Do you want to allow category titles to be added to the page title?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        $this->config_key_base . 'SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
        $this->config_key_base . 'SEO_TITLE_OVERRIDE' => [
          'title' => 'SEO Title Override?',
          'value' => 'True',
          'desc' => 'Do you want to allow category titles to be over-ridden by your SEO Titles (if set)?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
      ];
    }

  }
  