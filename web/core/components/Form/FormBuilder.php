<?php

namespace Nick\Form;

use Nick\Events\Event;
use Nick\Matter\MatterInterface;

/**
 * Class FormBuilder
 *
 * @package Nick
 */
class FormBuilder {

  /** @var MatterInterface $matter */
  protected $matter;

  /** @var array $values */
  protected $values = [];

  /** @var FormStateInterface $formState */
  protected $formState;

  /**
   * FormBuilder constructor.
   *
   * @param MatterInterface $matter
   */
  public function __construct(MatterInterface $matter) {
    $this->matter = $matter;

    /** @var FormStateInterface $formState */
    $this->formState = new FormState();
    $this->formState->setValues($this->values);
    $this->formState->save();
  }

  /**
   * Returns form elements in array format and fires an event.
   *
   * @return array
   */
  public function result() {
    $build = $this->build();
    $event = new Event('FormAlter');
    $event->fireEvent($build, ['form-' . $this->getMatter()->getType(), $this->formState]);
    return $build;
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
   * @param array $form
   * @param string $formId
   */
  public function submit(&$form, $formId) {
    // Set FormState values
    $this->formState->setValues($this->values);
    $this->formState->save();

    // Fire FormPreSubmitAlter event
    $preSubmitEvent = new Event('FormPreSubmitAlter');
    $preSubmitEvent->fireEvent($form, [$formId, $this->formState]);

    // Submit form
    // @TODO

    // Fire FormPostSubmitAlter event
    $postSubmitEvent = new Event('FormPostSubmitAlter');
    $postSubmitEvent->fireEvent($form, [$formId, $this->formState]);
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