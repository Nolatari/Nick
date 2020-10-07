<?php

namespace Nick\Manifest;

use Nick;
use Nick\Database\Database;
use Nick\Database\Result;
use Nick\Entity\EntityInterface;
use Nick\Entity\EntityManager;
use Nick\Entity\EntityRenderer;
use Nick\Event\Event;

/**
 * Class Manifest
 *
 * @package Nick\Manifest
 */
class Manifest implements ManifestInterface {

  /** @var string $type */
  protected string $type;

  /** @var array $limit */
  protected array $limit = ['limit' => 20, 'offset' => 0];

  /** @var array $condition */
  protected array $condition = [];

  /** @var array $orderBy */
  protected array $orderBy = [];

  /** @var array $fields */
  protected array $fields = ['id'];

  /** @var Database $query */
  protected Database $query;

  /**
   * Manifest constructor
   *
   * @param string $type
   */
  public function __construct(string $type) {
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
  public function result($massage = FALSE) {
    $query = $this->query();
    if (!$query instanceof Result) {
      return FALSE;
    }
    $results = $query->fetchAllAssoc();
    if ($massage) {
      /** @var EntityInterface $entity */
      $entity = EntityManager::getEntityClassFromType($this->getType());
      foreach ($results as $id => $values) {
        $results[$id] = $entity->massageProperties($values)->getValues();
        foreach ($results[$id] as &$field) {
          if (!$field instanceof EntityInterface) {
            continue;
          }
          $entityRenderer = new EntityRenderer($field);
          $field = $entityRenderer->render();
        }
      }
    }
    return $results;
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
  public function getFields() {
    return $this->fields;
  }

  /**
   * Builds and executes query.
   *
   * @return Result|bool
   */
  protected function query() {
    $query = Nick::Database()
      ->select('entity__' . $this->getType())
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
    $event = new Event('ManifestQueryAlter');
    $event->fire($query, [$this->getType()]);

    // Execute query
    if (!$result = $query->execute()) {
      return FALSE;
    }
    // Return Result if query was successful
    return $result;
  }

}