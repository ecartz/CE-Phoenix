<div class="col-sm-<?= MODULE_CONTENT_CARD_PRODUCTS_CONTENT_WIDTH ?> cm-i-card-products">
  <h4><?php printf(MODULE_CONTENT_CARD_PRODUCTS_HEADING, strftime('%B')); ?></h4>

  <div class="<?= IS_PRODUCT_PRODUCTS_DISPLAY_ROW ?>">
    <?php
    while ($card_product = tep_db_fetch_array($card_products_query)) {
      $product = new Product($card_product);
      ?>
      <div class="col mb-2">
        <div class="card h-100 is-product" <?= $product->build_data_attributes() ?>">
          <?php include $GLOBALS['oscTemplate']->map_to_template('product_card.php', 'component'); ?>
        </div>
      </div>
      <?php
    }
    ?>
  </div>
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
