<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_pi_options_attributes extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PI_OA_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    function execute() {
      global $currencies, $product;

      $content_width = (int)MODULE_CONTENT_PI_OA_CONTENT_WIDTH;

      $attributes = $product->get('attributes');
      if (count($attributes)) {
        $fr_input = $fr_required = '';
        if (MODULE_CONTENT_PI_OA_ENFORCE == 'True') {
          $fr_input    = FORM_REQUIRED_INPUT;
          $fr_required = 'required aria-required="true" ';
        }

        $options = [];
        $options_output = null;
        foreach ($attributes as $option_id => $attribute) {
          $option_choices = [];

          if (MODULE_CONTENT_PI_OA_HELPER == 'True') {
            $option_choices[] = ['id' => '', 'text' => MODULE_CONTENT_PI_OA_ENFORCE_SELECTION];
          }

          foreach ($attribute['values'] as $value_id => $value) {
            $text = $value['name'];
            if ($value['price'] != '0') {
              $text .= ' (' . $value['prefix']
                     . $currencies->display_price($value['price'], tep_get_tax_rate($product->get('tax_class_id')))
                     . ') ';
            }
            $option_choices[] = ['id' => $value_id, 'text' => $text];
          }

          if (is_string($_GET['products_id'])) {
            $selected_attribute = $_SESSION['cart']->contents[$_GET['products_id']]['attributes'][$option_id] ?? false;
          } else {
            $selected_attribute = false;
          }

          $options[] = [
            'id' => $option_id,
            'name' => $attribute['name'],
            'choices' => $option_choices,
            'selection' => $selected_attribute,
          ];
        }

        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_PI_OA_STATUS' => [
          'title' => 'Enable Options & Attributes',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_OA_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_OA_HELPER' => [
          'title' => 'Add Helper Text',
          'value' => 'True',
          'desc' => 'Should first option in dropdown be Helper Text?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_OA_ENFORCE' => [
          'title' => 'Enforce Selection',
          'value' => 'True',
          'desc' => 'Should customer be forced to select option(s)?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_OA_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '80',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }

