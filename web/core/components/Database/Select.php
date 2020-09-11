<?php

namespace Nick\Database;

use Nick\Matter\Matter;

/**
 * Class Select
 *
 * @package Nick\Database
 */
class Select extends Database {

  /**
   * Select constructor.
   *
   * @param      $table
   * @param null $alias
   * @param null $options
   */
  public function __construct($table, $alias = NULL, $options = NULL) {
    parent::__construct($this->condition_delimiter, $this->getDatabaseName());
    $this->addTable($table, $alias, $options);
  }

  /**
   * @param      $table
   * @param null $alias
   * @param      $options
   */
  public function addTable($table, $alias = NULL, $options = NULL) {
    $this->tables[] = ['table' => $table, 'alias' => $alias, 'options' => $options];
  }

  /**
   * fields method
   *
   * @param string $table_alias
   * @param array  $fields
   *
   * @return self
   */
  public function fields($table_alias = NULL, array $fields = []) {
    $this->fields[] = ['table_alias' => $table_alias, 'fields' => $fields];
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
   * orderBy method
   *
   * @param string $field
   * @param string $direction
   *
   * @return self
   */
  public function orderBy($field, $direction) {
    $this->orderby[] = ['field' => $field, 'direction' => $direction];
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
  public function limit(int $start = 0, int $end = Matter::CARDINALITY_DEFAULT) {
    $this->limit = "$start, $end";
    return $this;
  }

  /**
   * Builds and executes the query.
   *
   * @return Result|bool
   */
  public function execute() {
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
    $query = 'SELECT ' . $fields . ' FROM ' . $tables . $conditions . $orderby . $limit;

    return $this->executeQuery($query);
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
        $conditions .= $condition['field'] . ' ' . $condition['operator'] . ' ' . self::addQuotationMarks(self::Cleanse($this->database, '%' . $condition['value'] . '%'));
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

}