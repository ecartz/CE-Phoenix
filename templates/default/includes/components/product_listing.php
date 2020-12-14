<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  $listing_split = new splitPageResults($listing_sql, $num_list, 'p.products_id');

  if ($messageStack->size('product_action') > 0) {
    echo $messageStack->output('product_action');
  }
?>

<div class="contentText">

<?php
  if ($listing_split->number_of_rows > 0) {
    if ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) {
?>
  <div class="row align-items-center">
    <div class="col-sm-6 d-none d-sm-block">
      <?= $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS) ?>
    </div>
    <div class="col-sm-6">
      <?= $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(['page', 'info'])) ?>
    </div>
  </div>
<?php
    }
?>
    <div class="card mb-2 card-body alert-filters">
      <ul class="nav">
        <li class="nav-item dropdown">
          <a href="#" class="nav-link text-dark dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><?= TEXT_SORT_BY ?><span class="caret"></span></a>

          <div class="dropdown-menu">
            <?php
    foreach ($column_list as $i => $column) {
      if ($column_specifications[$column]['sortable']) {
        echo tep_create_sort_heading($_GET['sort'], $i+1, $column_specifications[$column]['heading']);
      }
    }
            ?>
          </div>

        </li>
      </ul>
    </div>

  <?php
    $listing_query = tep_db_query($listing_split->sql_query);

    $prod_list_contents = '';

    while ($listing = tep_db_fetch_array($listing_query)) {
      $listing['link'] = Product::build_link($listing['products_id'], tep_get_all_get_params(['products_id']));
      $product = new Product($listing);
      $card = [
        'show_buttons' => true,
      ];

      if (tep_not_null($product->get('seo_description'))) {
        $card['extra'] = '<div class="pt-2 font-weight-lighter">'
                       . $product->get('seo_description')
                       . '</div>' . PHP_EOL;
      }

      $prod_list_contents .= '<div class="col mb-2">';
        $prod_list_contents .= '<div class="card h-100 is-product"' . $product->get('data_attributes') . '>' . PHP_EOL;

          ob_start();
          include $GLOBALS['oscTemplate']->map_to_template('product_card.php', 'component');
          $prod_list_contents .= ob_get_clean();

        $prod_list_contents .= '</div>' . PHP_EOL;
      $prod_list_contents .= '</div>' . PHP_EOL;
    }

    echo $GLOBALS['OSCOM_Hooks']->call('filter', 'drawForm');

    echo '<div class="' . IS_PRODUCT_PRODUCTS_DISPLAY_ROW . '">' . PHP_EOL;
      echo $prod_list_contents;
    echo '</div>' . PHP_EOL;

    if ( ($listing_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>
  <div class="row align-items-center">
    <div class="col-sm-6 d-none d-sm-block">
      <?= $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS) ?>
    </div>
    <div class="col-sm-6">
      <?= $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(['page', 'info'])) ?>
    </div>
  </div>
<?php
    }
  } else {
    echo '<div class="alert alert-info" role="alert">' . TEXT_NO_PRODUCTS . '</div>';
  }
?>

</div>
