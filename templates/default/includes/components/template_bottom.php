<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  echo $OSCOM_Hooks->call('siteWide', 'injectBodyContentEnd');
?>

      </div> <!-- bodyContent //-->

<?php
  if ( $oscTemplate->hasBlocks('boxes_column_left') && ($tpl_template->getGridColumnWidth() > 0) ) {
?>

      <div id="columnLeft" class="col-md-<?php echo $tpl_template->getGridColumnWidth(); ?> order-6 order-md-1">
        <?php echo $oscTemplate->getBlocks('boxes_column_left'); ?>
      </div>

<?php
  }

  if ( $oscTemplate->hasBlocks('boxes_column_right') && ($tpl_template->getGridColumnWidth() > 0) ) {
?>

      <div id="columnRight" class="col-md-<?php echo $tpl_template->getGridColumnWidth(); ?> order-last">
        <?php echo $oscTemplate->getBlocks('boxes_column_right'); ?>
      </div>

<?php
  }
?>

    </div> <!-- row -->

<?php
  echo $OSCOM_Hooks->call('siteWide', 'injectBodyWrapperEnd');
?>

  </div> <!-- bodyWrapper //-->

<?php
  echo $OSCOM_Hooks->call('siteWide', 'injectBeforeFooter');

  require $oscTemplate->map_to_template('footer.php', 'component');

  echo $OSCOM_Hooks->call('siteWide', 'injectAfterFooter');
  echo $OSCOM_Hooks->call('siteWide', 'injectSiteEnd');

  echo $oscTemplate->getBlocks('footer_scripts');
?>

</body>
</html>
