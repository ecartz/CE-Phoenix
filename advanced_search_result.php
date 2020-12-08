<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  require 'includes/application_top.php';

  require language::map_to_translation('advanced_search.php');

  $error = false;

  if ( empty($_GET['keywords'])
    && (empty($_GET['dfrom']) || ($_GET['dfrom'] == DATE_FORMAT_STRING))
    && (empty($_GET['dto']) || ($_GET['dto'] == DATE_FORMAT_STRING))
    && !is_numeric($_GET['pfrom'] ?? null)
    && !is_numeric($_GET['pto'] ?? null)
    )
  {
    $error = true;

    $messageStack->add_session('search', ERROR_AT_LEAST_ONE_INPUT);
  } else {
    $dfrom = '';
    $dto = '';
    $pfrom = $_GET['pfrom'] ?? '';
    $pto = $_GET['pto'] ?? '';
    $keywords = '';

    if (isset($_GET['dfrom']) && ($_GET['dfrom'] !== DATE_FORMAT_STRING)) {
      $dfrom = $_GET['dfrom'];
    }

    if (isset($_GET['dto']) && ($_GET['dto'] !== DATE_FORMAT_STRING)) {
      $dto = $_GET['dto'];
    }

    if (isset($_GET['keywords'])) {
      $keywords = tep_db_prepare_input($_GET['keywords']);
    }

    $price_check_error = false;
    if (tep_not_null($pfrom)) {
      if (!settype($pfrom, 'double')) {
        $error = true;
        $price_check_error = true;

        $messageStack->add_session('search', ERROR_PRICE_FROM_MUST_BE_NUM);
      }
    }

    if (tep_not_null($pto)) {
      if (!settype($pto, 'double')) {
        $error = true;
        $price_check_error = true;

        $messageStack->add_session('search', ERROR_PRICE_TO_MUST_BE_NUM);
      }
    }

    if (!$price_check_error && is_float($pfrom) && is_float($pto)) {
      if ($pfrom >= $pto) {
        $error = true;

        $messageStack->add_session('search', ERROR_PRICE_TO_LESS_THAN_PRICE_FROM);
      }
    }

    if (tep_not_null($keywords)) {
      if (!tep_parse_search_string($keywords, $search_keywords)) {
        $error = true;

        $messageStack->add_session('search', ERROR_INVALID_KEYWORDS);
      }
    }
  }

  if (empty($dfrom) && empty($dto) && empty($pfrom) && empty($pto) && empty($keywords)) {
    $error = true;

    $messageStack->add_session('search', ERROR_AT_LEAST_ONE_INPUT);
  }

  if ($error) {
    tep_redirect(tep_href_link('advanced_search.php', tep_get_all_get_params(), 'NONSSL', true, false));
  }

  $select_str = <<<'EOSQL'
SELECT DISTINCT p.products_id, m.*, p.*, pd.*,
    p.products_quantity AS in_stock,
    IF(s.status, s.specials_new_products_price, NULL) AS specials_new_products_price,
    IF(s.status, s.specials_new_products_price, p.products_price) AS final_price,
    IF(s.status, 1, 0) AS is_special,
    IF(COALESCE(a.attribute_count, 0) > 0, 1, 0) AS has_attributes
EOSQL;

  if ( (DISPLAY_PRICE_WITH_TAX == 'true') && (tep_not_null($pfrom) || tep_not_null($pto)) ) {
    $select_str .= ", SUM(tr.tax_rate) AS tax_rate";
  }

  $from_str = " FROM products p LEFT JOIN manufacturers m USING(manufacturers_id) LEFT JOIN specials s ON p.products_id = s.products_id";

  if ( (DISPLAY_PRICE_WITH_TAX == 'true') && (tep_not_null($pfrom) || tep_not_null($pto)) ) {
    if (isset($_SESSION['customer_id'])) {
      $country_id = $customer->get_country_id();
      $zone_id = $customer->get_zone_id();
    } else {
      $country_id = STORE_COUNTRY;
      $zone_id = STORE_ZONE;
    }
    $from_str .= sprintf(<<<'EOSQL'
    LEFT JOIN tax_rates tr ON p.products_tax_class_id = tr.tax_class_id
    LEFT JOIN zones_to_geo_zones gz
      ON tr.tax_zone_id = gz.geo_zone_id
     AND (gz.zone_country_id IS NULL OR gz.zone_country_id = '0' OR gz.zone_country_id = %d)
     AND (gz.zone_id IS NULL OR gz.zone_id = '0' OR gz.zone_id = %d)
EOSQL
    , (int)$country_id, (int)$zone_id);
  }

  $from_str .= <<<'EOSQL'
    INNER JOIN products_description pd ON p.products_id = pd.products_id
    INNER JOIN products_to_categories p2c ON pd.products_id = p2c.categories_id
    INNER JOIN categories c ON p2c.categories_id = c.categories_id
    LEFT JOIN (SELECT products_id, COUNT(*) AS attribute_count FROM products_attributes GROUP BY products_id) a ON p.products_id = a.products_id
EOSQL;

  $where_str = sprintf(" WHERE p.products_status = 1 AND pd.language_id = %d ", (int)$_SESSION['languages_id']);

  if (isset($_GET['categories_id']) && tep_not_null($_GET['categories_id'])) {
    if (isset($_GET['inc_subcat']) && ($_GET['inc_subcat'] == '1')) {
      $subcategories_array = [];
      tep_get_subcategories($subcategories_array, $_GET['categories_id']);

      $where_str .= " AND (c.categories_id = " . (int)$_GET['categories_id'];

      foreach ($subcategories_array as $subcategory_id) {
        $where_str .= " OR c.categories_id = " . (int)$subcategory_id;
      }

      $where_str .= ")";
    } else {
      $where_str .= " AND c.categories_id = " . (int)$_GET['categories_id'];
    }
  }

  if (isset($_GET['manufacturers_id']) && tep_not_null($_GET['manufacturers_id'])) {
    $where_str .= " AND m.manufacturers_id = " . (int)$_GET['manufacturers_id'];
  }

  if (isset($search_keywords) && (count($search_keywords) > 0)) {
    $where_str .= " AND (";
    foreach ($search_keywords as $search_keyword) {
      switch ($search_keyword) {
        case '(':
        case ')':
        case 'and':
        case 'or':
          $where_str .= " " . strtoupper($search_keyword) . " ";
          break;
        default:
          $keyword = tep_db_prepare_input($search_keyword);
          $where_str .= "(";
          if ( (defined('MODULE_HEADER_TAGS_PRODUCT_META_KEYWORDS_STATUS')) && (MODULE_HEADER_TAGS_PRODUCT_META_KEYWORDS_STATUS == 'True') ) {
            $where_str .= "pd.products_seo_keywords LIKE '%" . tep_db_input($keyword) . "%' OR ";
          }
          $where_str .= "pd.products_name LIKE '%" . tep_db_input($keyword) . "%' OR p.products_model LIKE '%" . tep_db_input($keyword) . "%' OR m.manufacturers_name LIKE '%" . tep_db_input($keyword) . "%'";
          if (isset($_GET['search_in_description']) && ($_GET['search_in_description'] == '1')) {
            $where_str .= " OR pd.products_description LIKE '%" . tep_db_input($keyword) . "%'";
          }
          $where_str .= ')';
          break;
      }
    }
    $where_str .= " )";
  }

  if (tep_not_null($pfrom)) {
    if ($currencies->is_set($currency)) {
      $rate = $currencies->get_value($currency);

      $pfrom = $pfrom / $rate;
    }
  }

  if (tep_not_null($pto)) {
    if (isset($rate)) {
      $pto = $pto / $rate;
    }
  }

  if (DISPLAY_PRICE_WITH_TAX == 'true') {
    if ($pfrom > 0) {
      $where_str .= " AND (IF(s.status, s.specials_new_products_price, p.products_price) * IF(gz.geo_zone_id IS NULL, 1, 1 + (tr.tax_rate / 100) ) >= " . (double)$pfrom . ")";
    }
    if ($pto > 0) {
      $where_str .= " AND (IF(s.status, s.specials_new_products_price, p.products_price) * IF(gz.geo_zone_id IS NULL, 1, 1 + (tr.tax_rate / 100) ) <= " . (double)$pto . ")";
    }
  } else {
    if ($pfrom > 0) {
      $where_str .= " AND (IF(s.status, s.specials_new_products_price, p.products_price) >= " . (double)$pfrom . ")";
    }
    if ($pto > 0) {
      $where_str .= " AND (IF(s.status, s.specials_new_products_price, p.products_price) <= " . (double)$pto . ")";
    }
  }

  if ( (DISPLAY_PRICE_WITH_TAX == 'true') && (tep_not_null($pfrom) || tep_not_null($pto)) ) {
    $where_str .= " GROUP BY p.products_id, tr.tax_priority";
  }

  $listing_sql = $select_str . $from_str . $where_str;

  require $oscTemplate->map_to_template(__FILE__, 'page');

  require 'includes/application_bottom.php';
