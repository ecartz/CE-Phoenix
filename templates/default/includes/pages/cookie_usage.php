<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link('cookie_usage.php'));

  require $oscTemplate->map_to_template('template_top.php', 'component');
?>

<h1 class="display-4"><?php echo HEADING_TITLE; ?></h1>

<div class="contentContainer">
  <div class="card mb-2">
    <div class="card-header"><?php echo BOX_INFORMATION_HEADING; ?></div>
    <div class="card-body">
      <?php echo BOX_INFORMATION; ?>
    </div>
  </div>

  <div class="card mb-2 text-white bg-danger">
    <div class="card-body">
      <?php echo TEXT_INFORMATION; ?>
    </div>
  </div>

  <div class="buttonSet">
    <div class="text-right"><?php echo tep_draw_button(IMAGE_BUTTON_CONTINUE, 'fas fa-angle-right', tep_href_link('login.php'), null, null, 'btn-light btn-block btn-lg'); ?></div>
  </div>
</div>

<?php
  require $oscTemplate->map_to_template('template_bottom.php', 'component');
?>
