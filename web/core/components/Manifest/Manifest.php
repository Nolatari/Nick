<?php

namespace Nick\Manifest;

use Nick;
use Nick\Database\Database;
use Nick\Database\Result;
use Nick\Events\Event;

/**
 * Class Manifest
 *
 * @package Nick\Manifest
 */
class Manifest implements ManifestInterface {

  /** @var string $type */
  protected $type;

  /** @var array $limit */
  protected $limit = ['limit' => 20, 'offset' => 0];

  /** @var array $condition */
  protected $condition = [];

  /** @var array $orderBy */
  protected $orderBy = [];

  /** @var array $fields */
  protected $fields = ['id'];

  /** @var Database $query */
  protected $query;

  /**
   * {@inheritDoc}
   */
  public function __construct($type) {
    $this->setType($type);
  }

  /**
   * {@inheritDoc}
   */
  public function limit($limit, $offset = 0) {
    $this->setLimit([
      'offset' => $offset,
      'limit' => $limit,
    ]);
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function condition($field, $value, $operator = '=') {
    $this->setCondition($field, $value, $operator);
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function order($field, $direction = 'ASC') {
    $this->setOrderBy($field, $direction);
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function fields($fields = []) {
    $this->setFields($fields);
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function result() {
    $query = $this->query();
    if (!$query instanceof Database) {
      return FALSE;
    }
    return $query->fetchAllAssoc();
  }

  /**
   * Sets type variable.
   *
   * @param $type
   */
  protected function setType($type) {
    $this->type = $type;
  }

  /**
   * Gets type variable.
   *
   * @return string
   */
  protected function getType() {
    return $this->type ?? FALSE;
  }

  /**
   * Sets limit array
   *
   * @param array $limit
   */
  protected function setLimit($limit) {
    $this->limit = $limit;
  }

  /**
   * Sets limit array
   *
   * @return array $limit
   */
  protected function getLimit() {
    return $this->limit;
  }

  /**
   * Sets condition parameters
   *
   * @param $field
   * @param $value
   * @param $operator
   */
  protected function setCondition($field, $value, $operator) {
    $this->condition[] = [
      'field' => $field,
      'value' => $value,
      'operator' => $operator,
    ];
  }

  /**
   * Returns array of conditions
   *
   * @return array
   */
  protected function getConditions() {
    return $this->condition;
  }

  /**
   * Sets order field parameters
   *
   * @param string $field
   * @param string $direction
   */
  protected function setOrderBy($field, $direction) {
    $this->orderBy[] = [
      'field' => $field,
      'direction' => $direction,
    ];
  }

  /**
   * Returns array of order fields
   *
   * @return array
   */
  protected function getOrderBy() {
    return $this->orderBy;
  }

  /**
   * Sets array of fields
   *
   * @param array $fields
   */
  protected function setFields($fields = []) {
    $this->fields = $fields;
  }

  /**
   * Returns array of fields
   *
   * @return array
   */
  protected function getFields() {
    return $this->fields;
  }

  /**
   * Builds and executes query.
   *
   * @return Result|bool
   */
  protected function query() {
    $query = \Nick::Database();
    $query->select('matter__' . $this->getType())
      ->fields(NULL, $this->getFields());
    // Add conditions
    foreach ($this->getConditions() as $condition) {
      $query->condition($condition['field'], $condition['value'], $condition['operator']);
    }
    // Add order fields and directions
    foreach ($this->getOrderBy() as $order) {
      $query->orderBy($order['field'], $order['direction']);
    }
    // Add limits
    $limit = $this->getLimit();
    $query->limit($limit['offset'], $limit['limit']);

    // Fire alter event
    $event = new Event('ManifestAlter');
    $event->fireEvent($query);

    // Execute query
    if (!$result = $query->execute()) {
      return FALSE;
    }
    // Return Result if query was successful
    return $result;
  }

}