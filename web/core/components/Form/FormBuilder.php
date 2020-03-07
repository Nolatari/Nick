<?php

namespace Nick\Form;

use Nick\Matter\MatterInterface;

/**
 * Class FormBuilder
 *
 * @package Nick
 */
class FormBuilder {

  /** @var MatterInterface $matter */
  protected $matter;

  /**
   * FormBuilder constructor.
   *
   * @param MatterInterface $matter
   */
  public function __construct(MatterInterface $matter) {
    $this->matter = $matter;
  }

  /**
   * Returns form elements in array format.
   *
   * @return array
   */
  public function result() {
    return $this->build();
  }

  /**
   * Builds the form before returning to requester.
   *
   * @return array
   */
  protected function build() {
    $elements = [];
    foreach ($this->getFields() as $field => $values) {
      if (!isset($values['form'])) {
        continue;
      }
      $elements[$field] = $values['form'];
    }
    return $elements;
  }

  /**
   * Returns array of fields.
   *
   * @return array
   */
  protected function getFields() {
    return $this->getMatter()::fields();
  }

  /**
   * Returns content item object.
   *
   * @return MatterInterface
   */
  protected function getMatter() {
    return $this->matter;
  }

}