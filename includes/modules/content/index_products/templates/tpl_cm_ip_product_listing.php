<?php
  if (!empty($output)) {
?>

<div class="filter-list">
  <?php
    echo tep_draw_form('filter', 'index.php', 'get') . PHP_EOL;
    echo $output;
?>

  </form>
</div><br class="d-block d-sm-none">

<?php
  }
?>
<div class="col-sm-<?= MODULE_CONTENT_IP_PRODUCT_LISTING_CONTENT_WIDTH ?> cm-ip-product-listing">
  <?php include $GLOBALS['oscTemplate']->map_to_template('product_listing.php', 'component'); ?>
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
