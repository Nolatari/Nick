<?php

namespace Nick\Form;

use Nick;
use Nick\Core;
use Nick\Database\Result;

/**
 * Class FormState
 *
 * @package Nick\Form
 */
class FormState implements FormStateInterface {

  /** @var string $uuid */
  protected string $uuid;

  /** @var array $values */
  protected array $values = [];

  /**
   * FormState constructor.
   *
   * @param string|null $uuid
   */
  public function __construct($uuid = NULL) {
    if ($uuid !== NULL) {
      $this->setUUID($uuid);
      $this->populateValueArray();
    } else {
      // Create a unique UUID for this form state.
      $this->setUUID(Core::createUUID());
    }
  }

  /**
   * Returns uuid
   *
   * @return string
   */
  public function getUUID() {
    return $this->uuid;
  }

  /**
   * Sets uuid
   *
   * @param $uuid
   *
   * @return self
   */
  protected function setUUID($uuid) {
    $this->uuid = $uuid;

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function getValues(): array {
    return $this->values;
  }

  /**
   * {@inheritDoc}
   */
  public function setValues($values) {
    $this->values = $values;
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function get($key) {
    return $this->values[$key] ?? FALSE;
  }

  /**
   * {@inheritDoc}
   */
  public function set(string $key, string $value): self {
    $this->values[$key] = $value;
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function populateValueArray($return = FALSE): bool {
    $state_storage = Nick::Database()
      ->select('form_state')
      ->fields(NULL, ['data'])
      ->condition('uuid', $this->getUUID())
      ->execute();
    if (!$state_storage instanceof Result) {
      return FALSE;
    }

    if (!$result = $state_storage->fetchAllAssoc()) {
      return FALSE;
    }
    $this->values = unserialize($result[0]['data']);
    if ($return) {
      return count($this->getValues());
    }
    return TRUE;
  }

  /**
   * {@inheritDoc}
   */
  public function save() {
    if (!$this->populateValueArray()) {
      $query = Nick::Database()
        ->insert('form_state')
        ->values([
          $this->getUUID(),
          serialize($this->getValues()),
        ]);
    } else {
      $query = Nick::Database()
        ->update('form_state')
        ->condition('uuid', $this->getUUID())
        ->values([
          'data' => serialize($this->getValues()),
        ]);
    }
    return $query->execute();
  }

}
