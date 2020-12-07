  <a href="<?= $product->get('link') ?>"><?= tep_image('images/' . $product->get('image'), htmlspecialchars($product->get('name')), null, null, null, true, 'card-img-top') ?></a>
  <div class="card-body">
    <h5 class="card-title"><a href="<?= $product->get('link') ?>"><?= $product->get('name') ?></a></h5>
    <h6 class="card-subtitle mb-2 text-muted"><?= $product->hype_price() ?></h6>
    <?= $product->welcome('extra') ?>
  </div>

<?php
/**
 * osCommerce Online Merchant
 *
 * @copyright Copyright (c) 2020 osCommerce; http://www.oscommerce.com
 * @license BSD License; http://www.oscommerce.com/bsdlicense.txt
 */
?>
