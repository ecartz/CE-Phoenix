<div class="card mb-2 bm-product-notifications">
  <div class="card-header"><?= MODULE_BOXES_PRODUCT_NOTIFICATIONS_BOX_TITLE ?></div>
  <div class="list-group list-group-flush">
    <a class="list-group-item list-group-item-action" href="<?= $notification['link'] ?>"><i class="fas <?= $notification_exists ? 'fa-times' : 'fa-envelope' ?>"></i> <?= $notification['message'] ?></a>
  </div>
  <div class="card-footer"><a class="card-link" href="<?= tep_href_link('account_notifications.php') ?>"><?= MODULE_BOXES_PRODUCT_NOTIFICATIONS_VIEW ?></a></div>
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