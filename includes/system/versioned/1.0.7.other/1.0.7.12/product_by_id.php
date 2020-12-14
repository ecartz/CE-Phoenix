<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  class product_by_id {

    public static function build($product_id, $get_parameters = null) {
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
EOSQL
        , (int)$product_id, (int)$_SESSION['languages_id']));

      if ($product = tep_db_fetch_array($product_query)) {
        if (!empty($get_parameters)) {
          $product['link'] = Product::build_link($product_id, $get_parameters);
        }

        return new Product($product);
      }

      return new Product(['status' => 0, 'id' => (int)$product_id]);
    }

    public static function build_extended($product_id, $get_parameters = null) {
      if ( empty($product_id) ) {
        return new Product();
      }

      $sql = <<<'EOSQL'
SELECT pd.*, p.*,
    IF(s.status, s.specials_new_products_price, NULL) AS specials_new_products_price,
    IF(s.status, s.specials_new_products_price, p.products_price) AS final_price,
    p.products_quantity AS in_stock,
    IF(s.status, 1, 0) AS is_special
EOSQL;

      if (isset($_SESSION['customer_id'])) {
        $sql .= <<<'EOSQL'
,
    IF(pn.customers_id IS NULL, 0, 1) AS has_notification

EOSQL;
      }

      $sql .= <<<'EOSQL'
  FROM products_description pd
    INNER JOIN products p ON pd.products_id = p.products_id
    LEFT JOIN specials s ON p.products_id = s.products_id

EOSQL;
      if (isset($_SESSION['customer_id'])) {
        $sql .= sprintf(<<<'EOSQL'
    LEFT JOIN products_notifications pn ON p.products_id = pn.products_id AND pn.customers_id = %d

EOSQL
        , (int)$_SESSION['customer_id']);
      }
      $sql .= <<<'EOSQL'
  WHERE p.products_status = 1 AND p.products_id = %d AND pd.language_id = %d
EOSQL;
      $product_query = tep_db_query(sprintf($sql, (int)$product_id, (int)$_SESSION['languages_id']));

      if ($product = tep_db_fetch_array($product_query)) {
        if (!empty($get_parameters)) {
          $product['link'] = Product::build_link($product_id, $get_parameters);
        }

        return new extended_product($product);
      }

      return new Product(['status' => 0, 'id' => (int)$product_id]);
    }

  }
