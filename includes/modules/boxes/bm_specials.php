<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class bm_specials extends abstract_block_module {

    const CONFIG_KEY_BASE = 'MODULE_BOXES_SPECIALS_';

    function execute() {
      if ($product = random_special::build()) {
        $box = [
          'parameters' => ['product_card.php', 'component'],
          'classes' => 'is-product bm-specials',
          'title' => '<a href="' . $product->get('link') . '">' . $product->get('name') . '</a>',
          'attributes' => $product->build_data_attributes(),
        ];

        $tpl_data = [
          'group' => $this->group,
          'file' => 'box.php',
          'type' => 'component',
        ];
        include 'includes/modules/block_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_BOXES_SPECIALS_STATUS' => [
          'title' => 'Enable Specials Module',
          'value' => 'True',
          'desc' => 'Do you want to add the module to your shop?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_BOXES_SPECIALS_CONTENT_PLACEMENT' => [
          'title' => 'Content Placement',
          'value' => 'Right Column',
          'desc' => 'Should the module be loaded in the left or right column?',
          'set_func' => "tep_cfg_select_option(['Left Column', 'Right Column'], ",
        ],
        'MODULE_BOXES_SPECIALS_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '0',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }

  }
