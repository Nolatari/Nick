<?php

namespace Nick\Form;

use Nick\Core;

/**
 * Class FormState
 *
 * @package Nick\Form
 */
class FormState {

  /** @var string $uuid */
  protected $uuid;

  /**
   * FormState constructor.
   */
  public function __construct() {
    // Create a unique UUID for this form state!
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

}