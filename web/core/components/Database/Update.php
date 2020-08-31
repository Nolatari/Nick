<?php

namespace Nick\Database;

use Nick\Matter\MatterInterface;

/**
 * Class Update
 *
 * @package Nick\Database
 */
class Update extends Database {

  /**
   * Update constructor.
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
   * values method
   *
   * @param array $data
   *
   * @return self
   */
  public function values($data = []) {
    $this->values = $data;
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
   * Builds and executes the query.
   *
   * @return bool
   */
  public function execute() {
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
    $query = 'UPDATE ' . $tables . ' SET ' . $fields . $conditions;

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

}