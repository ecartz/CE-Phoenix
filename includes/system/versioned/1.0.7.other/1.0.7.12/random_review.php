<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  class random_review {

    public static function build() {
      $random_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT RAND() * COUNT(*) AS `offset`
  FROM reviews r
   INNER JOIN reviews_description rd ON r.reviews_id = rd.reviews_id
   INNER JOIN products p ON p.products_id = r.products_id
   INNER JOIN products_description pd ON p.products_id = pd.products_id AND rd.languages_id = pd.language_id
  WHERE p.products_status = 1 AND r.reviews_status = 1 AND rd.languages_id = %d
  ORDER BY r.reviews_id DESC
EOSQL
        , (int)$_SESSION['languages_id']));

      $random_selection = tep_db_fetch_array($random_query);
      if (!$random_selection) {
        return false;
      }

      $product_query = tep_db_query(sprintf(<<<'EOSQL'
SELECT pd.*, p.*,
    IF(s.status, s.specials_new_products_price, NULL) AS specials_new_products_price,
    IF(s.status, s.specials_new_products_price, p.products_price) AS final_price,
    p.products_quantity AS in_stock,
    IF(s.status, 1, 0) AS is_special,
    IF(COALESCE(a.attribute_count, 0) > 0, 1, 0) AS has_attributes,
    SUBSTRING(rd.reviews_text, 1, 60) AS reviews_text,
    r.reviews_rating
  FROM reviews r
    INNER JOIN reviews_description rd ON r.reviews_id = rd.reviews_id
    INNER JOIN products p ON p.products_id = r.products_id
    INNER JOIN products_description pd ON p.products_id = pd.products_id AND rd.languages_id = pd.language_id
    LEFT JOIN specials s ON p.products_id = s.products_id
    LEFT JOIN (SELECT products_id, COUNT(*) AS attribute_count FROM products_attributes GROUP BY products_id) a ON p.products_id = a.products_id
  WHERE p.products_status = 1 AND pd.language_id = %d
  ORDER BY r.reviews_id DESC LIMIT 1 OFFSET %d
EOSQL
        , (int)$_SESSION['languages_id'], (int)$random_selection['offset']));

      if ($product = tep_db_fetch_array($product_query)) {
        return new Product($product);
      }

      return false;
    }

  }
