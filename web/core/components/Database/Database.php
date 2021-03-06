<?php

namespace Nick\Database;

use mysqli;
use Nick;
use Nick\Settings;
use Nick\SqlFormatter;
use Nick\StringManipulation;

/**
 * Class Database
 *
 * @package Nick
 */
class Database {

  /** @var string $db */
  protected $db;

  /** @var mysqli $database */
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
  protected $condition_delimiter = 'AND';

  /** @var bool $use_cache */
  protected $use_cache;

  /**
   * Database constructor
   *
   * @param string $condition_delimiter
   * @param null   $database
   */
  public function __construct($condition_delimiter = 'AND', $database = NULL) {
    $this->condition_delimiter = $condition_delimiter;
    $this->setDatabaseName($database ?? Settings::get('database.database'));
    $this->use_cache = $database == NULL ? FALSE : TRUE;
    $this->connect();
    $this->reset();
  }

  /**
   * connect function
   *     Will use caching if no custom database name was given, if a custom database was given then it will initiate
   *     a new mysqli instance.
   *
   * @return Database
   */
  protected function connect(): Database {
    $this->database = \Nick::Cache()->getData('connection', '\\mysqli', NULL, [], [
      Settings::get('database.hostname'),
      Settings::get('database.username'),
      Settings::get('database.password'),
      $this->getDatabaseName(),
      Settings::get('database.port') ?? 3306,
    ]);
    return $this;
  }

  /**
   * @return string|null
   */
  public function getDatabaseName(): ?string {
    return $this->db;
  }

  /**
   * @param string $database
   */
  protected function setDatabaseName(string $database) {
    $this->db = $database;
  }

  /**
   * Static escape function
   *
   * @param mysqli $connection
   * @param mixed  $value
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
   * @param $value
   *
   * @return string
   */
  public static function addQuotationMarks($value): string {
    if (!is_null($value)) {
      // Cast value to string first, as integers could be passed
      $value = (string)$value;
      if (substr($value, 0, 1) !== "'" && substr($value, -1, 1) !== "'") {
        return "'" . $value . "'";
      }
    }

    return $value;
  }

  /**
   * @return array
   */
  public static function getFieldTypes(): array {
    return [
      'INT', 'TINYINT', 'SMALLINT', 'MEDIUMINT', 'BIGINT', 'DECIMAL', 'FLOAT', 'DOUBLE', 'BIT',
      'CHAR', 'VARCHAR', 'BINARY', 'VARBINARY', 'TINYBLOB', 'BLOB', 'MEDIUMBLOB', 'LONGBLOB', 'TINYTEXT', 'TEXT', 'MEDIUMTEXT', 'LONGTEXT', 'ENUM', 'SET',
      'DATE', 'TIME', 'DATETIME', 'TIMESTAMP', 'YEAR',
      'GEOMETRY', 'POINT', 'LINESTRING', 'POLYGON', 'GEOMETRYCOLLECTION', 'MULTILINESTRING', 'MULTIPOINT', 'MULTIPOLYGON',
    ];
  }

  /**
   * @param string $table
   * @param null   $alias
   * @param null   $options
   *
   * @return Select
   */
  public function select(string $table, $alias = NULL, $options = NULL): Select {
    return new Select($table, $alias, $options, $this->condition_delimiter, $this->getDatabaseName());
  }

  /**
   * @param string $table
   * @param null   $alias
   * @param null   $options
   *
   * @return Update
   */
  public function update(string $table, $alias = NULL, $options = NULL): Update {
    return new Update($table, $alias, $options);
  }

  /**
   * @param string $table
   * @param null   $alias
   * @param null   $options
   *
   * @return Insert
   */
  public function insert(string $table, $alias = NULL, $options = NULL): Insert {
    return new Insert($table, $alias, $options);
  }

  /**
   * @param string $table
   * @param null   $alias
   * @param null   $options
   *
   * @return Delete
   */
  public function delete(string $table, $alias = NULL, $options = NULL): Delete {
    return new Delete($table, $alias, $options);
  }

  /**
   * @param string $query
   *
   * @return Query
   */
  public function query(string $query): Query {
    return new Query($query);
  }

  /**
   * Adds a new, or modifies an existing, field in a given table with given options.
   *
   * @param string $table
   * @param string $field
   * @param array  $options
   *
   * @return Nick\Database\Query
   */
  public function field(string $table, string $field, array $options): Query {
    $result = \Nick::Database()->query("SHOW COLUMNS FROM `" . $table . "` LIKE '" . $field . "'");
    $exists = $result->count() > 0;

    if ($exists) {
      // If it exists, modify column
      $query = \Nick::Database()->query('ALTER TABLE ' . $table
        . ' MODIFY COLUMN ' . \Nick::Database()::createFieldQuery($field, $options));
    } else {
      // If it doesn't exist, add column
      $query = \Nick::Database()->query('ALTER TABLE ' . $table
        . ' ADD ' . \Nick::Database()::createFieldQuery($field, $options));
    }

    return $query;
  }

  /**
   * Removes a given field from given table.
   *
   * @param string $table
   * @param string $field
   *
   * @return Query
   */
  public function removeField(string $table, string $field): Query {
    return \Nick::Database()->query('ALTER TABLE ' . $table
      . ' DROP COLUMN' . $field);
  }

  /**
   * @param string $field_name
   * @param array  $options
   *
   * @return string
   */
  public static function createFieldQuery(string $field_name, array $options): string {
    if (isset($options['type'])
      && StringManipulation::contains($options['type'], 'text')
      && StringManipulation::contains($options['type'], 'blob')
      && !isset($options['length'])) {
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
  protected function executeQuery(string $query) {
    if (Settings::get('debugging')) {
      echo '<pre style="color:#fff;">';
      echo SqlFormatter::format($query);
      echo '</pre>';
    }

    if (empty($query)) {
      return FALSE;
    }
    $result = $this->database->query($query);
    if (Settings::get('debugging')) {
      if ($this->database->error !== '') {
        d($this->database->error);
      }
    }
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
