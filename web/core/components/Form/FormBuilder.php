<?php

namespace Nick\Form;

use Nick\Event\Event;
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
    $event->fire($build, ['form-' . $this->getMatter()->getType(), $this->formState]);
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
   * Fire events and submit handler
   *
   * @param array  $form
   * @param string $formId
   */
  public function submit(array &$form, string $formId) {
    // Set FormState values
    $this->getFormState()->setValues($this->values);
    $this->getFormState()->save();

    // Fire FormPreSubmitAlter event
    $preSubmitEvent = new Event('FormPreSubmitAlter');
    $preSubmitEvent->fire($form, [$formId, $this->getFormState()]);

    // Fire submit handler
    $this->submitForm();

    // Fire FormPostSubmitAlter event
    $postSubmitEvent = new Event('FormPostSubmitAlter');
    $postSubmitEvent->fire($form, [$formId, $this->getFormState()]);
  }

  /**
   * Default submit handler does nothing because there is nothing to handle!
   */
  public function submitForm() {

  }

  /**
   * @return FormState|FormStateInterface
   */
  public function getFormState() {
    return $this->formState;
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