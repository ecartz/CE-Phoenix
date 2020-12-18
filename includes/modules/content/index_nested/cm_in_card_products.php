<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_in_card_products extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_IN_CARD_PRODUCTS_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    public function execute() {
      $card_products_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT DISTINCT %s
 FROM products p
  LEFT JOIN specials s ON p.products_id = s.products_id
  INNER JOIN products_description pd ON p.products_id = pd.products_id
  INNER JOIN products_to_categories p2c ON p.products_id = p2c.products_id
  INNER JOIN categories c ON p2c.categories_id = c.categories_id
  LEFT JOIN (SELECT products_id, COUNT(*) AS attribute_count FROM products_attributes GROUP BY products_id) a ON p.products_id = a.products_id
 WHERE p.products_status = 1 AND c.parent_id = %d AND pd.language_id = %d
 ORDER BY p.products_id DESC LIMIT %d
EOSQL
        , Product::COLUMNS,
          (int)$GLOBALS['current_category_id'],
          (int)$_SESSION['languages_id'],
          (int)MODULE_CONTENT_IN_CARD_PRODUCTS_MAX_DISPLAY));

      $num_card_products = tep_db_num_rows($card_products_query);
      if ($num_card_products > 0) {
        $card = [
          'show_buttons' => true,
        ];

        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_IN_CARD_PRODUCTS_STATUS' => [
          'title' => 'Enable New Products Module',
          'value' => 'True',
          'desc' => 'Do you want to enable this module?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_IN_CARD_PRODUCTS_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in? (12 = full width, 6 = half width).',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_IN_CARD_PRODUCTS_MAX_DISPLAY' => [
          'title' => 'Maximum Display',
          'value' => '6',
          'desc' => 'Maximum Number of products that should show in this module?',
        ],
        'MODULE_CONTENT_IN_CARD_PRODUCTS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '300',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
