<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class Any {

    public static function is_empty($v = null) {
      if (is_null($v)) {
        return true;
      }

      if (is_array($v)) {
        return [] === $v;
      }

      if (is_string($v)) {
        return Text::is_empty($v);
      }

      return Text::is_empty((string)$v);
    }

    public static function prepare($input) {
      if (is_string($input)) {
        return Text::prepare($input);
      }

      if (is_array($input)) {
        return array_map([$this, 'prepare_input'], $input);
      }

      return $input;
    }

  }
