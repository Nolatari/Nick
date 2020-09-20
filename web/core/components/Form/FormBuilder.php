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

  /** @var string $id */
  protected string $id;

  /** @var MatterInterface|null $matter */
  protected $matter = NULL;

  /** @var array $values */
  protected array $values = [];

  /** @var array $fields */
  protected array $fields = [];

  /** @var FormStateInterface $formState */
  protected FormStateInterface $formState;

  /**
   * FormBuilder constructor.
   *
   * @param MatterInterface|null $matter
   */
  public function __construct(MatterInterface $matter = NULL) {
    if (!is_null($matter)) {
      $this->matter = $matter;
      $this->setFields($matter::fields());
    }

    /** @var FormStateInterface $formState */
    $this->formState = new FormState();
    $this->formState->setValues($this->values);
    $this->formState->save();
  }

  /**
   * Returns form elements in array format and fires an event.
   *
   * @return string
   */
  public function result(): string {
    $build = $this->build();
    $event = new Event('FormAlter');
    $event->fire($build, ['form-' . $this->getId(), $this->formState]);
    $render = '<form method="post" name="form-' . $this->getId() . '">';
    foreach ($build as $key => $element) {
      $element['key'] = $key;
      $element['formId'] = $this->getId();
      $element['attributes']['value'] = $this->getFormState()->get($key) ?: NULL;
      if ($element['attributes']['value'] === NULL && isset($element['default_value'])) {
        $element['attributes']['value'] = $element['default_value'];
      }
      $type = ucfirst($element['type']);
      $className = '\\Nick\\Form\\FormElements\\' . $type;
      /** @var FormElement $elementClass */
      $elementClass = new $className();
      $render .= $elementClass->render($element);
    }
    $render .= '</form>';
    return $render;
  }

  /**
   * Builds the form before returning to requester.
   *
   * @return array
   */
  public function build(): array {
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
   * @return FormStateInterface
   */
  public function getFormState(): FormStateInterface {
    return $this->formState;
  }

  /**
   * Returns array of fields.
   *
   * @return array
   */
  protected function getFields(): array {
    return $this->fields;
  }

  /**
   * Sets fields array.
   *
   * @param array $values
   *
   * @return self
   */
  public function setFields(array $values): self {
    $this->fields = $values;

    return $this;
  }

  /**
   * Returns form ID.
   *
   * @return string
   */
  protected function getId(): string {
    return $this->id;
  }

  /**
   * Sets form ID.
   *
   * @param string $id
   *
   * @return self
   */
  public function setId(string $id): self {
    $this->id = $id;

    return $this;
  }

  /**
   * Returns content item object.
   *
   * @return MatterInterface|null
   */
  protected function getMatter(): ?MatterInterface {
    return $this->matter;
  }

}