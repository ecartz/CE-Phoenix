<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class Database extends mysqli {

    public function __construct($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE) {
      parent::__construct($server, $username, $password, $database);

      if ( is_null($this->connect_error) ) {
        $this->set_charset('utf8mb4');
      }

      @parent::query("SET SESSION sql_mode=''");
    }

    public function report_error($query) {
      if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
        error_log("ERROR: [$this->errno] $this->error\n" . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
      }

      die('<font color="#000000"><strong>' . $this->errno . ' - ' . $this->error . '<br><br>' . $query . '<br><br><small><font color="#ff0000">[TEP STOP]</font></small><br><br></strong></font>');
    }

    public function query($query, $resultmode = null) {
      if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
        error_log('QUERY: ' . $query . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
      }

      $result = parent::query($query, $resultmode) or $this->report_error($query);

      return $result;
    }

    public static function normalize_value($value) {
      switch (strtoupper($value)) {
        case 'NOW()':
          return 'NOW()';
        case 'NULL':
          return 'NULL';
        default:
          return "'" . $this->real_escape_string($value) . "'";
      }
    }

    public function perform($table, $data, $action = 'insert', $parameters = '') {
      if ($action == 'insert') {
        $query = 'INSERT INTO ' . $table . ' (' . implode(', ', array_keys($data))
               . ') VALUES ('
               . implode(', ', array_map([$this, 'normalize_value'], $data)) . ')';
      } elseif ($action == 'update') {
        $query = 'UPDATE ' . $table . ' SET '
               . implode(', ', array_map(function ($c, $v) {
          return "$c = $v";
        }, array_keys($data), array_map([$this, 'normalize_value'], $data)))
               . ' WHERE ' . $parameters;
      }

      return $this->query($query);
    }

    public function escape(string $input) {
      return $this->real_escape_string($input);
    }

    public static function prepare_input($input) {
      return Any::prepare($input);
    }

    public function fetch_all($db_query) {
      if (!($db_query instanceof mysqli_result) && is_string($db_query)) {
        $db_query = $this->query($db_query);
      }

      if (method_exists($db_query, 'fetch_all')) {
        return $db_query->fetch_all();
      }

      $results = [];
      while ($result = $db_query->fetch_assoc()) {
        $results[] = $result;
      }

      return $results;
    }

  }
