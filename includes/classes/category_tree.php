<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2018 osCommerce; http://www.oscommerce.com
 * @license GNU General Public License; http://www.oscommerce.com/gpllicense.txt
 */

  class category_tree extends Tree {

    protected static $_parents = [];

    public function __construct() {
      static $_category_tree_data;

      if ( isset($_category_tree_data) ) {
        $this->_data = $_category_tree_data;
      } else {
        $categories_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT c.categories_id, c.parent_id, c.categories_image, cd.categories_name, cd.categories_description, cd.categories_seo_description, cd.categories_seo_title
 FROM categories c INNER JOIN categories_description cd ON c.categories_id = cd.categories_id
 WHERE cd.language_id = %d
 ORDER BY c.parent_id, c.sort_order, cd.categories_name
EOSQL
          , (int)$_SESSION['languages_id']));

        while ( $category = tep_db_fetch_array($categories_query) ) {
          $this->_data[$category['parent_id']][$category['categories_id']] = [
            'name'            => $category['categories_name'],
            'image'           => $category['categories_image'],
            'description'     => $category['categories_description'],
            'seo_description' => $category['categories_seo_description'],
            'seo_title'       => $category['categories_seo_title'],
          ];

          static::$_parents[$category['categories_id']] = $category['parent_id'];
        }

        $_category_tree_data = $this->_data;
      }
    }

    public function build_path_link($path) {
      return tep_href_link('index.php', 'cPath=' . $path);
    }

    public function get_selections($categories = [], $parent_id = '0', $indent = '') {
      if (!is_array($categories)) {
        $categories = [];
      }

      $category_tree =& Guarantor::ensure_global('category_tree');
      foreach ($category_tree->get_descendants($parent_id) as $category_id) {
        $categories[] = [
          'id' => $category_id,
          'text' => $indent . $category_tree->get($category_id, 'name'),
        ];
      }

      return $categories;
    }

  }
