<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class database_core extends mysqli {

    /**
     * Connect to a database, with the correct charset and sql_mode set.
     *
     * @param string $server
     * @param string $username
     * @param string $password
     * @param string $database
     */
    public function __construct($server = DB_SERVER, $username = DB_SERVER_USERNAME, $password = DB_SERVER_PASSWORD, $database = DB_DATABASE) {
      parent::__construct($server, $username, $password, $database);

      if ( is_null($this->connect_error) ) {
        $this->set_charset('utf8mb4');
      }

      @parent::query("SET SESSION sql_mode=''");
    }

    /**
     * Report a fatal error if a database query fails.
     *
     * @param string $sql
     */
    public function report_error($sql) {
      if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
        error_log("ERROR: [$this->errno] $this->error\n" . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
      }

      die('<font color="#000000"><strong>' . $this->errno . ' - ' . $this->error . '<br><br>' . $sql . '<br><br><small><font color="#ff0000">[TEP STOP]</font></small><br><br></strong></font>');
    }

    /**
     * Run SQL query, logging and reporting errors if necessary.
     *
     * @param string $sql
     * @param string $resultmode
     * @return boolean
     */
    public function query($sql, $resultmode = MYSQLI_STORE_RESULT) {
      if (defined('STORE_DB_TRANSACTIONS') && (STORE_DB_TRANSACTIONS == 'true')) {
        error_log('QUERY: ' . $sql . "\n", 3, STORE_PAGE_PARSE_TIME_LOG);
      }

      return parent::query($sql, $resultmode) or $this->report_error($sql);
    }

    /**
     * Escape a string for safe insertion into a SQL statement.
     *
     * @param string $input
     * @return string
     */
    public function escape(string $input) {
      return $this->real_escape_string($input);
    }

  }
