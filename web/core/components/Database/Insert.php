<?php

namespace Nick\Database;

/**
 * Class Insert
 *
 * @package Nick\Database
 */
class Insert extends Database {

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
    foreach ($data as $field => $value) {
      $this->values[] = $value;
    }
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
    $values = '';
    foreach ($this->values as $value) {
      if ($values !== '') {
        $values .= ', ';
      }
      $values .= self::addQuotationMarks(self::Cleanse($this->database, $value));
    }
    $query = 'INSERT INTO ' . $tables . ' VALUES (' . $values . ')';

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

}