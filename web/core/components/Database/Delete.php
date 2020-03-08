<?php

namespace Nick\Database;

/**
 * Class Delete
 *
 * @package Nick\Database
 */
class Delete extends Database {

  /**
   * Delete constructor.
   *
   * @param $table
   * @param null $alias
   * @param null $options
   */
  public function __construct($table, $alias = NULL, $options = NULL) {
    parent::__construct($this->condition_delimiter, $this->getDatabaseName());
    $this->addTable($table, $alias, $options);
  }

  /**
   * @param $table
   * @param null $alias
   * @param $options
   */
  public function addTable($table, $alias = NULL, $options = NULL) {
    $this->tables[] = ['table' => $table, 'alias' => $alias, 'options' => $options];
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
    $conditions = $this->getConditions();
    $query = 'DELETE FROM ' . $tables . $conditions;

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