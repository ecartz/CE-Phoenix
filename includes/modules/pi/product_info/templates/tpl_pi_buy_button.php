<div class="col-sm-<?= (int)PI_BUY_CONTENT_WIDTH ?> pi-buy-button mt-2">
  <?=
  tep_draw_button(PI_BUY_BUTTON_TEXT, 'fas fa-shopping-cart', null, 'primary', array('params' => $data_attributes), 'btn-success btn-block btn-lg btn-product-info btn-buy')
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
