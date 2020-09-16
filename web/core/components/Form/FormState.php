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
  protected $uuid;

  /** @var array $values */
  protected $values = [];

  /**
   * FormState constructor.
   */
  public function __construct() {
    // Create a unique UUID for this form state.
    $this->setUUID(Core::createUUID());
  }

  /**
   * Returns uuid
   *
   * @return string
   */
  protected function getUUID() {
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
  public function populateValueArray($return = FALSE): bool {
    $state_storage = Nick::Database()
      ->select('form_state')
      ->condition('uuid', $this->getUUID())
      ->execute();
    if (!$state_storage instanceof Result) {
      return FALSE;
    }

    $this->values = $state_storage->fetchAllAssoc();
    if ($return) {
      return count($this->getValues());
    }
    return TRUE;
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
  public function save() {
    if (!$this->populateValueArray(TRUE)) {
      $query = Nick::Database()
        ->insert('form_state')
        ->values([
          $this->getUUID(),
          $this->getValues(),
        ]);
    } else {
      $query = Nick::Database()
        ->update('form_state')
        ->condition('uuid', $this->getUUID())
        ->values([
          'values' => $this->getValues(),
        ]);
    }
    return $query->execute();
  }

}
