<?php

namespace Nick;

use mysqli_result;
use Nick\Matter\MatterInterface;
use mysqli;

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
    $this->db = $database ?? $this->getSetting('database')['database'];
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
   * Query function
   *
   * @param string $query
   *
   * @return self
   */
  public function query($query) {
    if (isset($this->settings['debugging']) && $this->settings['debugging']) {
      d($query);
    }
    if ($result = $this->database->query($query)) {
      $this->query = [
        'useQuery' => TRUE,
        'query' => $result,
      ];
      $this->result = $result;
    }

    return $this;
  }

  /**
   * select method
   *
   * @param string $table
   * @param string $alias
   * @param array $options
   *
   * @return self
   */
  public function select($table, $alias = NULL, $options = NULL) {
    $this->method = 'SELECT';
    $this->tables[] = ['table' => $table, 'alias' => $alias, 'options' => $options];
    return $this;
  }

  /**
   * insert method
   *
   * @param string $table
   * @param string $alias
   * @param array $options
   *
   * @return self
   */
  public function insert($table, $alias = NULL, $options = NULL) {
    $this->method = 'INSERT';
    $this->tables[] = ['table' => $table, 'alias' => $alias, 'options' => $options];
    return $this;
  }

  /**
   * update method
   *
   * @param string $table
   * @param string $alias
   * @param array $options
   *
   * @return self
   */
  public function update($table, $alias = NULL, $options = NULL) {
    $this->method = 'UPDATE';
    $this->tables[] = ['table' => $table, 'alias' => $alias, 'options' => $options];
    return $this;
  }

  /**
   * delete method
   *
   * @param string $table
   * @param string $alias
   * @param array $options
   *
   * @return self
   */
  public function delete($table, $alias = NULL, $options = NULL) {
    $this->method = 'DELETE';
    $this->tables[] = ['table' => $table, 'alias' => $alias, 'options' => $options];
    return $this;
  }

  /**
   * join method
   *
   * @param string $table
   * @param string $alias
   * @param array $options
   *
   * @return self
   */
  public function join($table, $alias = NULL, $options = NULL) {
    $this->tables[] = ['table' => $table, 'alias' => $alias, 'options' => $options];
    return $this;
  }

  /**
   * condition method
   *
   * @param string $field
   * @param string $value
   * @param string $operator
   *
   * @return self
   */
  public function condition($field, $value = NULL, $operator = '=') {
    $this->conditions[] = [
      'field' => $field,
      'operator' => $operator,
      'value' => $value,
    ];
    return $this;
  }

  /**
   * fields method
   *
   * @param string $table_alias
   * @param array $fields
   *
   * @return self
   */
  public function fields($table_alias = NULL, array $fields = []) {
    if ($this->method === 'SELECT') {
      $this->fields[] = ['table_alias' => $table_alias, 'fields' => $fields];
    }
    return $this;
  }

  /**
   * values method
   *
   * @param array $data
   *
   * @return self
   */
  public function values($data = []) {
    if ($this->method === 'INSERT') {
      foreach ($data as $field => $value) {
        $this->values[] = $value;
      }
    } elseif ($this->method === 'UPDATE') {
      $this->values = $data;
    }
    return $this;
  }

  /**
   * orderBy method
   *
   * @param string $field
   * @param string $direction
   *
   * @return self
   */
  public function orderBy($field, $direction) {
    if ($this->method === 'SELECT') {
      $this->orderby[] = ['field' => $field, 'direction' => $direction];
    }
    return $this;
  }

  /**
   * limit method
   *
   * @param int $start
   * @param int $end
   *
   * @return self
   */
  public function limit(int $start = 0, int $end = 15) {
    $this->limit = "$start, $end";
    return $this;
  }

  /**
   * @return string
   */
  protected function getTables() {
    $tables = '';
    foreach ($this->tables as $table) {
      if ($tables !== '') {
        $tables .= ', ';
      }
      $tables .= $table['table'];
      if ($table['alias'] !== NULL) {
        $tables .= ' AS ' . $table['alias'];
      }
    }
    return $tables;
  }

  /**
   * @return string
   */
  protected function getConditions() {
    $conditions = '';
    foreach ($this->conditions as $condition) {
      if ($conditions !== '') {
        $conditions .= ' ' . $this->condition_delimiter . ' ';
      }
      if (strtoupper($condition['operator']) === 'LIKE') {
        $conditions .= $condition['field'] . ' ' . $condition['operator'] . ' ' . self::addQuotationMarks(self::Cleanse($this->database,  '%' . $condition['value'] . '%'));
      } else {
        $conditions .= $condition['field'] . ' ' . $condition['operator'] . ' ' . self::addQuotationMarks(self::Cleanse($this->database, $condition['value']));
      }
    }
    if ($conditions !== '') {
      $conditions = ' WHERE ' . $conditions;
    }
    return $conditions;
  }

  /**
   * @return string
   */
  protected function getOrderBy() {
    $orderby = '';
    foreach ($this->orderby as $order) {
      if ($orderby !== '') {
        $orderby .= ', ';
      }
      $orderby .= $order['field'] . ' ' . $order['direction'];
    }
    if ($orderby !== '') {
      $orderby = ' ORDER BY ' . $orderby;
    }
    return $orderby;
  }

  /**
   * @return string
   */
  protected function getLimit() {
    $limit = '';
    if ($this->limit !== NULL) {
      $limit = ' LIMIT ' . $this->limit;
    }
    return $limit;
  }

  /**
   * Builds the query if method is SELECT
   *
   * @return string
   */
  protected function buildSelectQuery() {
    $tables = $this->getTables();
    $fields = '';
    foreach ($this->fields as $field_array) {
      foreach ($field_array['fields'] as $field) {
        if ($fields !== '') {
          $fields .= ', ';
        }
        if (!is_null($field_array['table_alias'])) {
          $fields .= $field_array['table_alias'] . '.' . self::Cleanse($this->database, $field);
        } else {
          $fields .= self::Cleanse($this->database, $field);
        }
      }
    }
    $conditions = $this->getConditions();
    $orderby = $this->getOrderBy();
    if ($fields === '') {
      $fields = '*';
    }
    $limit = $this->getLimit();
    return 'SELECT ' . $fields . ' FROM ' . $tables . $conditions . $orderby . $limit;
  }

  /**
   * Builds the query if method is INSERT
   *
   * @return string
   */
  protected function buildInsertQuery() {
    $tables = $this->getTables();
    $values = '';
    foreach ($this->values as $value) {
      if ($values !== '') {
        $values .= ', ';
      }
      $values .= self::addQuotationMarks(self::Cleanse($this->database, $value));
    }
    return 'INSERT INTO ' . $tables . ' VALUES (' . $values . ')';
  }

  /**
   * Builds the query if method is UPDATE
   *
   * @return string
   */
  protected function buildUpdateQuery() {
    $tables = $this->getTables();
    $fields = '';
    foreach ($this->values as $field => $value) {
      if ($fields !== '') {
        $fields .= ', ';
      }
      if ($value instanceof MatterInterface) {
        $value = $value->id();
      }
      $fields .= $field . ' = ' . self::addQuotationMarks(self::Cleanse($this->database, $value));
    }
    $conditions = $this->getConditions();
    if ($fields === '') {
      $fields = '*';
    }
    return 'UPDATE ' . $tables . ' SET ' . $fields . $conditions;
  }

  /**
   * Builds the query if method is DELETE
   *
   * @return string
   */
  protected function buildDeleteQuery() {
    $tables = $this->getTables();
    $conditions = $this->getConditions();
    return 'DELETE FROM ' . $tables . $conditions;
  }

  /**
   * execute query method
   *
   * @return bool
   */
  public function execute() {
    if (is_array($this->query) && $this->query['useQuery'] === TRUE) {
      if ($this->result === $this->query['query']) {
        return TRUE;
      }
    } else {
      $query = '';
      if ($this->method === 'SELECT') {
        $query = $this->buildSelectQuery();
      } elseif ($this->method === 'INSERT') {
        $query = $this->buildInsertQuery();
      } elseif ($this->method === 'UPDATE') {
        $query = $this->buildUpdateQuery();
      } elseif ($this->method === 'DELETE') {
        $query = $this->buildDeleteQuery();
      }

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
        return TRUE;
      }
    }

    $this->reset();
    return FALSE;
  }

  /**
   * fetchAllAssoc method
   *
   * @param string|null $key
   *
   * @return array|bool
   */
  public function fetchAllAssoc($key = NULL) {
    if (!$this->result instanceof mysqli_result) {
      return FALSE;
    }

    $records = [];
    while ($record = mysqli_fetch_assoc($this->result)) {
      if ($key !== NULL) {
        $records[$record[$key]] = $record;
      } else {
        $records[] = $record;
      }
    }

    $this->result = '';
    return $records;
  }

  /**
   * Resets \Nick\Database object.
   */
  public function reset() {
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
