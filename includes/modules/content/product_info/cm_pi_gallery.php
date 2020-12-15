<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class cm_pi_gallery extends abstract_executable_module {

    const CONFIG_KEY_BASE = 'MODULE_CONTENT_PI_GALLERY_';

    public function __construct() {
      parent::__construct(__FILE__);
    }

    public function execute() {
      global $product, $oscTemplate;

      $content_width = (int)MODULE_CONTENT_PI_GALLERY_CONTENT_WIDTH;
      $thumbnail_width = MODULE_CONTENT_PI_GALLERY_CONTENT_WIDTH_EACH;

      $pi_image = $pi_thumb = null;

      if (tep_not_null($product->get('image'))) {
        $active_image = ['image' => $product->get('image'), 'htmlcontent' => $product->get('name')];
        $other_images = $product->get('images');

        $album_name = sprintf(MODULE_CONTENT_PI_GALLERY_ALBUM_NAME, $product->get('name'));
        $album_exit = MODULE_CONTENT_PI_GALLERY_ALBUM_CLOSE;

        $modal_size = MODULE_CONTENT_PI_GALLERY_MODAL_SIZE;

        $tpl_data = [ 'group' => $this->group, 'file' => __FILE__ ];
        include 'includes/modules/content/cm_template.php';
      }
    }

    protected function get_parameters() {
      return [
        'MODULE_CONTENT_PI_GALLERY_STATUS' => [
          'title' => 'Enable Gallery Module',
          'value' => 'True',
          'desc' => 'Should this module be shown on the product info page?',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_GALLERY_CONTENT_WIDTH' => [
          'title' => 'Content Width',
          'value' => '4',
          'desc' => 'What width container should the content be shown in?',
          'set_func' => "tep_cfg_select_option(['12', '11', '10', '9', '8', '7', '6', '5', '4', '3', '2', '1'], ",
        ],
        'MODULE_CONTENT_PI_GALLERY_CONTENT_WIDTH_EACH' => [
          'title' => 'Thumbnail Width',
          'value' => 'col-4 col-sm-6 col-lg-4',
          'desc' => 'What width container should each thumbnail be shown in? Default:  XS 3 each row, SM/MD 2 each row, LG/XL 3 each row.',
        ],
        'MODULE_CONTENT_PI_GALLERY_MODAL_SIZE' => [
          'title' => 'Modal Popup Size',
          'value' => 'modal-md',
          'desc' => 'Choose the size of the Popup.  sm = small, md = medium etc.',
          'set_func' => "tep_cfg_select_option(['modal-sm', 'modal-md', 'modal-lg', 'modal-xl'], ",
        ],
        'MODULE_CONTENT_PI_GALLERY_SWIPE_ARROWS' => [
          'title' => 'Show Swipe Arrows',
          'value' => 'True',
          'desc' => 'Swipe Arrows make for a better User Experience in some cases.',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_GALLERY_INDICATORS' => [
          'title' => 'Show Indicators',
          'value' => 'True',
          'desc' => 'Indicators allow users to jump from image to image without having to swipe.',
          'set_func' => "tep_cfg_select_option(['True', 'False'], ",
        ],
        'MODULE_CONTENT_PI_GALLERY_SORT_ORDER' => [
          'title' => 'Sort Order',
          'value' => '65',
          'desc' => 'Sort order of display. Lowest is displayed first.',
        ],
      ];
    }
  }
