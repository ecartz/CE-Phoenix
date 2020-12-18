<div class="col-sm-<?= (int)MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_CONTENT_WIDTH ?> cm-pi-also-purchased">
  <h4><?= MODULE_CONTENT_PRODUCT_INFO_ALSO_PURCHASED_PUBLIC_TITLE ?></h4>

  <div class="<?= IS_PRODUCT_PRODUCTS_DISPLAY_ROW ?>">
    <?php
    $card = [
      'show_buttons' => true,
    ];

    while ($also_purchased = tep_db_fetch_array($also_purchased_query)) {
      $product = new Product($also_purchased);
      ?>
      <div class="col mb-2">
        <div class="card h-100 is-product" <?= $product->get('data_attributes') ?>">
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
