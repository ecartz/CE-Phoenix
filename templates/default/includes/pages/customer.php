<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('customer.php', '', 'SSL'));

  require $oscTemplate->map_to_template('template_top.php', 'component');

  if ($messageStack->size('customer') > 0) {
    echo $messageStack->output('customer');
  }
?>

<div class="contentContainer">
  <div class="row"><?php echo $oscTemplate->getContent('customer'); ?></div>
</div>


<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
