<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2020 osCommerce

  Released under the GNU General Public License
*/

  class Database extends mysqli {

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
     * Generate results from a prepared query
     * when mysqli_statement->get_result not available.
     *
     * @param mysqli_statement $statement
     * @return Generator
     */
    protected function generate_results($statement) {
      $fields = [];
      $meta = $statement->result_metadata();
      while ($field = $meta->fetch_field()) {
        // prefix variable name to avoid clashes with columns
        // named statement, fields, meta, field, variable_name
        $variable_name = "cepv_{$field->name}";
        $$variable_name = null;
        $fields[$field->name] = &$$variable_name;
      }

      call_user_func_array([$statement, 'bind_result'], $fields);
      while ($statement->fetch()) {
        yield array_replace([], $fields);
      }
    }

    /**
     * Generate prepared results for a statement.
     *
     * @param string $sql
     * @param array ...$bindings
     * @return Generator
     */
    public function lasso(string $sql, ...$bindings) {
      $statement = $this->prepare($sql) or $this->report_error($sql);
      $statement->bind_param(...$bindings);

      foreach (str_split(array_shift($bindings)) as $i => $type) {
        switch ($type) {
          case 'i':
            $bindings[$i] = (int)$bindings[$i];
            break;
          case 'd':
            if ((false === strpos($bindings[$i], '.'))
             && (false === stripos($bindings[$i], 'e')))
            {
              $bindings[$i] .= '.0';
            }

            $bindings[$i] = (float)$bindings[$i];
            break;
        }
      }

      $statement->execute();

      if (method_exists($statement, 'get_result')) {
        $result = $statement->get_result();

        while ($data = $result->fetch_assoc()) {
          yield $data;
        }
      } else {
        yield from $this->generate_results($statement);
      }

      $statement->close();
    }

    /**
     * Escape any non-whitelisted string.
     *
     * @param string $value
     * @return string
     */
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

    /**
     * Perform an insert or update on the specified table.
     *
     * @param string $table
     * @param array $data Column names as keys and values as values.
     * @param string $action Defaults to insert.
     * @param string $parameters Only needed for updates; the where clause.
     * @return boolean
     */
    public function perform($table, $data, $action = 'insert', $parameters = '') {
      if ($action == 'insert') {
        $query = 'INSERT INTO ' . $table . ' (' . implode(', ', array_keys($data))
               . ') VALUES ('
               . implode(', ', array_map([$this, 'normalize_value'], $data)) . ')';
      } elseif ($action == 'update') {
        $query = 'UPDATE ' . $table . ' SET '
               . implode(', ', array_map(function ($column, $value) {
          return "$column = $value";
        }, array_keys($data), array_map([$this, 'normalize_value'], $data)))
               . ' WHERE ' . $parameters;
      }

      return $this->query($query);
    }

    public function escape(string $input) {
      return $this->real_escape_string($input);
    }

    /**
     * Fetch all the results from a query.
     *
     * @param mysqli_result|string $db_query
     * @return []
     */
    public function fetch_all($db_query) {
      if (!($db_query instanceof mysqli_result) && is_string($db_query)) {
        $db_query = $this->query($db_query);
      }

      if (method_exists($db_query, 'fetch_all')) {
        return $db_query->fetch_all(MYSQLI_ASSOC);
      }

      $results = [];
      while ($result = $db_query->fetch_assoc()) {
        $results[] = $result;
      }

      return $results;
    }

  }
