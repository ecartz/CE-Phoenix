<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_pi_review_stars extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PI_REVIEW_STARS_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $product;

      $pid = (int)$product->get('id');
      $review_link = tep_href_link('ext/modules/content/reviews/write.php', "products_id=$pid");

      $review_stars_array = [];
      $review_count = count($product->get('reviews'));
      if ($review_count > 0) {
        $review_stars_array[] = tep_draw_stars((int)$product->get('review_rating'));

        if (1 === (int)$review_count) {
          $review_stars_array[] = sprintf(MODULE_CONTENT_PI_REVIEW_STARS_COUNT_ONE, (int)$review_count);
        } else {
          $review_stars_array[] = sprintf(MODULE_CONTENT_PI_REVIEW_STARS_COUNT, (int)$review_count);
        }

        $do_review = MODULE_CONTENT_PI_REVIEW_STARS_DO_REVIEW;
      } else {
        $review_stars_array[] = sprintf(MODULE_CONTENT_PI_REVIEW_STARS_COUNT, 0);

        $do_review = MODULE_CONTENT_PI_REVIEW_STARS_DO_FIRST_REVIEW;
      }

      $content_width = (int)MODULE_CONTENT_PI_REVIEW_STARS_CONTENT_WIDTH;

      $tpl_data = ['group' => $this->group, 'file' => __FILE__];
      include 'includes/modules/content/cm_template.php';
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_PI_REVIEW_STARS_STATUS' => [
          'title' => 'Enable Review Stars/Link Module',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_REVIEW_STARS_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_REVIEW_STARS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '55',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
