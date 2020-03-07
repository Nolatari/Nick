<?php

namespace Nick\Database;

use mysqli;
use Nick\Settings;
use Nick\SqlFormatter;

/**
 * Class Database
 *
 * @package Nick
 */
class Database extends Settings {

  /** @var string $db */
  protected $db;

  /** @var mixed $database */
  protected $database;

  /** @var mixed $result */
  protected $result;

  /** @var array $query */
  protected $query;

  /** @var string $method */
  protected $method;

  /** @var array $tables */
  protected $tables;

  /** @var array $fields */
  protected $fields;

  /** @var array $values */
  protected $values;

  /** @var array $joins */
  protected $joins;

  /** @var array $orderby */
  protected $orderby;

  /** @var string $limit */
  protected $limit;

  /** @var array $conditions */
  protected $conditions;

  /** @var string $condition_delimiter */
  protected $condition_delimiter;

  /**
   * Database constructor
   *
   * @param string $condition_delimiter
   * @param null $database
   */
  public function __construct($condition_delimiter = 'AND', $database = NULL) {
    parent::__construct();
    $this->condition_delimiter = $condition_delimiter;
    $this->setDatabaseName($database ?? $this->getSetting('database')['database']);
    $this->connect();
    $this->reset();
  }

  /**
   * connect function
   *
   * @return Database
   */
  protected function connect() {
    $mysqliData = [
      $this->getSetting('database')['hostname'],
      $this->getSetting('database')['username'],
      $this->getSetting('database')['password'],
      $this->getDatabaseName(),
    ];

    $this->database = \Nick::Cache()->getData('connection', '\\mysqli', NULL, [], $mysqliData);
    return $this;
  }

  /**
   * @return string|null
   */
  public function getDatabaseName() {
    return $this->db;
  }

  /**
   * @param string $database
   */
  protected function setDatabaseName($database) {
    $this->db = $database;
  }

  /**
   * Static escape function
   *
   * @param mysqli $connection
   * @param string $value
   *
   * @return string
   */
  public static function Cleanse(mysqli $connection, $value) {
    if (!$escaped_string = mysqli_real_escape_string($connection, $value)) {
      return $value;
    }
    return $escaped_string;
  }

  /**
   * Static function to add single quotation marks in case
   * this was not done when building the query.
   *
   * @param string $value
   *
   * @return string
   */
  public static function addQuotationMarks($value) {
    if (!is_null($value)) {
      if (substr($value, 0, 1) !== "'" && substr($value, -1, 1) !== "'") {
        return "'" . $value . "'";
      }
    }

    return $value;
  }

  /**
   * @return array
   */
  public static function getFieldTypes() {
    return [
      'INT', 'TINYINT', 'SMALLINT', 'MEDIUMINT', 'BIGINT', 'DECIMAL', 'FLOAT', 'DOUBLE', 'BIT',
      'CHAR', 'VARCHAR', 'BINARY', 'VARBINARY', 'TINYBLOB', 'BLOB', 'MEDIUMBLOB', 'LONGBLOB', 'TINYTEXT', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT', 'ENUM', 'SET',
      'DATE', 'TIME', 'DATETIME', 'TIMESTAMP', 'YEAR',
      'GEOMETRY', 'POINT', 'LINESTRING', 'POLYGON', 'GEOMETRYCOLLECTION', 'MULTILINESTRING', 'MULTIPOINT', 'MULTIPOLYGON',
    ];
  }

  /**
   * @param string $table
   * @param null $alias
   * @param null $options
   *
   * @return Select
   */
  public function select($table, $alias = NULL, $options = NULL) {
    return new Select($table, $alias, $options);
  }

  /**
   * @param string $table
   * @param null $alias
   * @param null $options
   *
   * @return Update
   */
  public function update($table, $alias = NULL, $options = NULL) {
    return new Update($table, $alias, $options);
  }

  /**
   * @param string $table
   * @param null $alias
   * @param null $options
   *
   * @return Insert
   */
  public function insert($table, $alias = NULL, $options = NULL) {
    return new Insert($table, $alias, $options);
  }

  /**
   * @param string $table
   * @param null $alias
   * @param null $options
   *
   * @return Delete
   */
  public function delete($table, $alias = NULL, $options = NULL) {
    return new Delete($table, $alias, $options);
  }

  public function query($query) {
    return new Query($query);
  }

  /**
   * @param $field_name
   * @param $options
   *
   * @return string
   */
  public static function createFieldQuery($field_name, $options) {
    if (isset($options['type']) && strpos($options['type'], 'text') !== FALSE && !isset($options['length'])) {
      $field = '`' . $field_name . '` ' . $options['type'] . ' ';
    } else {
      $field = '`' . $field_name . '` ' . $options['type'] . '(' . ($options['length'] ?? 255) . ') ';
    }
    $field .= ($options['null'] ?? 'NOT NULL') . ' ';
    $field .= isset($options['unique']) && $options['unique'] == TRUE ? 'UNIQUE ' : '';
    $field .= (isset($options['default_value']) ? 'DEFAULT \'' . $options['default_value'] . '\'' : '') . ' ';

    return trim($field);
  }

  /**
   * @param string $query
   *
   * @return Result|bool
   */
  protected function executeQuery($query) {
    if ($this->getSetting('debugging')) {
      echo '<pre style="color:#fff;">';
      echo SqlFormatter::format($query);
      echo '</pre>';
    }

    if (empty($query)) {
      return FALSE;
    }
    $result = $this->database->query($query);
    //d($this->database->error);
    if ($result !== FALSE) {
      $this->result = $result;

      $this->reset();
      return new Result($result);
    }

    $this->reset();
    return FALSE;
  }

  /**
   * Resets \Nick\Database object.
   */
  protected function reset() {
    $this->tables = [];
    $this->fields = [];
    $this->joins = [];
    $this->values = [];
    $this->orderby = [];
    $this->conditions = [];
    $this->method = '';
    $this->query = [
      'useQuery' => FALSE,
      'query' => [],
    ];
    $this->joins = [
      'isJoined' => FALSE,
      'join' => [],
    ];
  }

}