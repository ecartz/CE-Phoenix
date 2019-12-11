<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2019 osCommerce

  Released under the GNU General Public License
*/

  const ADMIN_AUTOLOAD_PATH = DIR_FS_CACHE . 'admin_autoload_index.cache';

  function tep_build_admin_autoload_index($modules_directory_length) {
    $class_files = [];
    
    tep_find_all_files_under(DIR_FS_ADMIN . 'includes/modules', $class_files);
    tep_find_all_files_under(DIR_FS_ADMIN . 'includes/classes', $class_files);
    
    // some classes do not follow either naming standard relating the class name and file name
    $exception_mappings = [
      'Password_hash' => 'passwordhash',
    ];

    foreach ($exception_mappings as $class_name => $filename) {
      $class_files[$class_name] = $class_files[$filename];
      unset($class_files[$filename]);
    }

    if (!empty($class_files) && (is_writable(ADMIN_AUTOLOAD_PATH) || (!file_exists(ADMIN_AUTOLOAD_PATH) && is_writable(DIR_FS_CACHE)))) {
      file_put_contents(ADMIN_AUTOLOAD_PATH, serialize($class_files), LOCK_EX);
      
      // if we cache the admin index,
      if (is_writable(CATALOG_AUTOLOAD_PATH)) {
        if (is_writable(DIR_FS_CACHE)) {
          // delete the catalog index so it gets rebuilt as well -- if we can
          unlink(CATALOG_AUTOLOAD_PATH);
        } else {
          // or rebuild it in place if we can't
          tep_build_catalog_autoload_index();
        }
      }
    }

    return $class_files;
  }

  function tep_autoload_admin($class) {
    static $class_files;
    static $modules_directory_length;

    if (!isset($class_files)) {
      $modules_directory_length = strlen(DIR_FS_ADMIN . 'includes/modules');

      if (file_exists(ADMIN_AUTOLOAD_PATH) && time() - filemtime(ADMIN_AUTOLOAD_PATH) < 60) {
        $class_files = tep_load_autoload_index(ADMIN_AUTOLOAD_PATH);
      } else {
        $class_files = tep_build_admin_autoload_index($modules_directory_length);
      }
    }

    // convert camelCase class names to snake_case filenames
    $class = strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $class));

    if (isset($class_files[$class])) {
      if (isset($GLOBALS['language']) && DIR_FS_ADMIN . 'includes/modules' === substr($class_files[$class], 0, $modules_directory_length)) {
        $language_file = DIR_FS_ADMIN . 'includes/languages/'. $GLOBALS['language'] . '/modules' . substr($class_files[$class], $modules_directory_length);
        if (file_exists($language_file)) {
          include $language_file;
        }
      }

      require $class_files[$class];
    }
  }
