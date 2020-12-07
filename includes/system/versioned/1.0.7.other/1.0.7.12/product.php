<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */

  class Product {

    protected $_data = [];

    public function __construct($data = ['status' => 0]) {
      foreach ($data as $key => $value) {
        $trimmed_key = tep_ltrim_once($key, 'products_');

        $this->_data[isset($data[$trimmed_key]) ? $key : $trimmed_key] = $value;
      }

      if (isset($this->_data['id']) && !isset($this->_data['link'])) {
        $this->_data['link'] = tep_href_link('product_info.php', 'products_id=' . (int)$this->_data['id']);
      }
    }

    public function has($key) {
      return isset($this->_data[$key]) || array_key_exists($this->_data, $key);
    }

    public function get($key) {
      return $this->_data[$key];
    }

    public function welcome($key) {
      return $this->has($key) ? $this->get($key) : null;
    }

    public function set($key, $value) {
      $this->_data[$key] = $value;
    }

    public function get_data() {
      return $this->_data;
    }

    public function build_data_attributes($data = []) {
      $data['data-is-special'] = $this->get('is_special');
      $data['data-product-price'] = $this->format_raw();
      $data['data-product-manufacturer'] = $this->get('manufacturers_id');

      $this->_data['data-attributes'] = implode(array_map(function ($key, $value) {
        return ' ' . htmlspecialchars($key) . '="' . htmlspecialchars($value) . '"';
      }, array_keys($data), $data));

      return $this->_data['data-attributes'];
    }

    public function hype_price($show_special_price = true) {
      if ($show_special_price && ($this->get('is_special') == 1)) {
        return sprintf(
          IS_PRODUCT_SHOW_PRICE_SPECIAL,
          $this->format('price'),
          $this->format());
      }

      return sprintf(IS_PRODUCT_SHOW_PRICE, $this->format());
    }

    public function format($price = 'final_price') {
      return $GLOBALS['currencies']->display_price($this->get($price), tep_get_tax_rate($this->get('tax_class_id')));
    }

    public function format_raw($price = 'final_price') {
      return $GLOBALS['currencies']->display_raw($this->get($price), tep_get_tax_rate($this->get('tax_class_id')));
    }

  }
