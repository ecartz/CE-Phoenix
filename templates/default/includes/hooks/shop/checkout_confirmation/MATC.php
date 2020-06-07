<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

class hook_shop_checkout_confirmation_MATC {

  function listen_injectFormDisplay() {
    $this->load_lang();

    $title = ENTRY_MATC;
    $checkbox = tep_draw_checkbox_field('matc', 1, false, 'required="required" aria-required="true" class="custom-control-input" id="inputMATC"');
    $label = ENTRY_MATC_TEXT;

    $matc = <<<EOHTML
<div class="form-group row align-items-center">
  <div class="col-form-label col-sm-3 text-left text-sm-right">{$title}</div>
  <div class="col-sm-9 pl-5 custom-control custom-switch">
    {$checkbox}
    <label for="inputMATC" class="custom-control-label text-muted"><small>{$label}</small></label>
  </div>
</div>
EOHTML;

    return $matc;
  }

  function listen_injectSiteEnd() {
    $this->load_lang();

    $close_button   = MATC_BUTTON_CLOSE;
    $p_modal_title  = MATC_PRIVACY_MODAL_TITLE;
    $p_modal_text   = MATC_PRIVACY_MODAL_TEXT;
    $tc_modal_title = MATC_TERMS_MODAL_TITLE;
    $tc_modal_text  = MATC_TERMS_MODAL_TEXT;

    $modal = <<<EOHTML
<div class="modal fade" id="PModal" tabindex="-1" role="dialog" aria-labelledby="PModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="PModalLabel">{$p_modal_title}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">{$p_modal_text}</div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{$close_button}</button></div>
    </div>
  </div>
</div>
<div class="modal fade" id="TCModal" tabindex="-1" role="dialog" aria-labelledby="TCModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="TCModalLabel">{$tc_modal_title}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">{$tc_modal_text}</div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">{$close_button}</button></div>
    </div>
  </div>
</div>
EOHTML;

    return $modal;
  }

  function load_lang() {
    require 'includes/languages/' . $_SESSION['language'] . '/hooks/shop/siteWide/MATC.php';
  }

}
