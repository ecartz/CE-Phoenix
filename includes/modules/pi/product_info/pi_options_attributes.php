<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class pi_options_attributes extends abstract_module {

    const CONFIG_KEY_BASE = 'PI_OA_';

    public $group = 'pi_modules_c';
    public $content_width;

    function __construct() {
      parent::__construct();

      $this->group = basename(dirname(__FILE__));

      $this->description .= '<div class="alert alert-warning">' . MODULE_CONTENT_BOOTSTRAP_ROW_DESCRIPTION . '</div>';
      $this->description .= '<div class="alert alert-info">' . cm_pi_modular::display_layout() . '</div>';

      if ( $this->enabled ) {
        $this->group = 'pi_modules_' . strtolower(PI_OA_GROUP);
        $this->content_width = (int)PI_OA_CONTENT_WIDTH;
      }
    }

    function getOutput() {
      global $currencies, $product;

      $attributes = $product->get('attributes');
      if (count($attributes)) {
        $content_width = (int)PI_OA_CONTENT_WIDTH;

        $fr_input = $fr_required = '';
        if (PI_OA_ENFORCE == 'True') {
          $fr_input    = FORM_REQUIRED_INPUT;
          $fr_required = 'required aria-required="true" ';
        }

        $tax_rate = tep_get_tax_rate($product->get('tax_class_id'));

        $options = [];
        foreach ($attributes as $option_id => $option) {
          $choices = [];

          if (PI_OA_HELPER == 'True') {
            $choices[] = ['id' => '', 'text' => PI_OA_ENFORCE_SELECTION];
          }

          foreach ($option['values'] as $value_id => $value) {
            $text = $value['name'];
            if ($value['price'] != '0') {
              $text .= ' (' . $value['prefix']
                     . $currencies->display_price($value['price'], $tax_rate)
                     . ') ';
            }

            $choices[] = ['id' => $value_id, 'text' => $text];
          }

          if (is_string($_GET['products_id'])) {
            $selected_attribute = $_SESSION['cart']->contents[$_GET['products_id']]['attributes'][$option_id] ?? false;
          } else {
            $selected_attribute = false;
          }

          $options[] = [
            'id' => $option_id,
            'name' => $option['name'],
            'menu' => tep_draw_pull_down_menu(
                        'id[' . $option_id . ']',
                        $choices,
                        $selected_attribute,
                        $fr_required . 'id="input_' . $option_id . '"'
                      ),
          ];
        }

        $tpl_data = ['group' => $this->group, 'file' => __FILE__];
        include 'includes/modules/block_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'PI_OA_STATUS' => [
          'title' => 'Enable Options & Attributes',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'PI_OA_GROUP' => [
          'title' => 'Module Display',
          'value' => 'C',
          'desc' => 'Where should this module display on the product info page?',
          'set_func' => "tep_cfg_select_option(['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I'], ",
        ],
        'PI_OA_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '12',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'PI_OA_HELPER' => [
          'title' => 'Add Helper Text',
          'value' => 'True',
          'desc' => 'Should first option in dropdown be Helper Text?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'PI_OA_ENFORCE' => [
          'title' => 'Enforce Selection',
          'value' => 'True',
          'desc' => 'Should customer be forced to select option(s)?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'PI_OA_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '310',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }

