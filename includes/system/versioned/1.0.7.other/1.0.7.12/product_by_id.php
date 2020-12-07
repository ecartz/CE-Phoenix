<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  class product_by_id {

    public static function build($product_id) {
      if ( empty($product_id) ) {
        return new Product();
      }

      $product_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT pd.*, p.*,
    IF(s.status, s.specials_new_products_price, NULL) AS specials_new_products_price,
    IF(s.status, s.specials_new_products_price, p.products_price) AS final_price,
    p.products_quantity AS in_stock,
    IF(s.status, 1, 0) AS is_special,
    IF(COALESCE(a.attribute_count, 0) > 0, 1, 0) AS has_attributes
  FROM products_description pd
    INNER JOIN products p ON pd.products_id = p.products_id
    LEFT JOIN specials s ON p.products_id = s.products_id
    LEFT JOIN (SELECT products_id, COUNT(*) AS attribute_count FROM products_attributes GROUP BY products_id) a ON p.products_id = a.products_id
  WHERE p.products_status = 1 AND p.products_id = %d AND pd.language_id = %d
  ORDER BY p.products_id DESC
EOSQL
        , (int)$product_id, (int)$_SESSION['languages_id']));

      if ($data = tep_db_fetch_array($product_query)) {
        $product = [];

        foreach ($data as $key => $value) {
          $trimmed_key = tep_ltrim_once($key, 'products_');
          $product[isset($data[$trimmed_key]) ? $key : $trimmed_key] = $value; 
        }
        $product['link'] = tep_href_link('product_info.php', 'products_id=' . (int)$product['id']);

        return new Product($product);
      }

      return new Product(['status' => 0, 'id' => (int)$product_id]);
    }

  }
