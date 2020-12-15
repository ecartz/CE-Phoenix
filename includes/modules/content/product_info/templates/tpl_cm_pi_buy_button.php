<div class="col-sm-<?= (int)MODULE_CONTENT_PI_BUY_CONTENT_WIDTH ?> text-right cm-pi-buy-button">
  <?=
  tep_draw_button(MODULE_CONTENT_PI_BUY_BUTTON_TEXT, 'fas fa-shopping-cart', null, 'primary', ['params' => $data_attributes], 'btn-success btn-block btn-lg btn-product-info btn-buy')
  . tep_draw_hidden_field('products_id', (int)$product->get('id'))
  ?>
</div>

<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/
?>
