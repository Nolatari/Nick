<?php

namespace Nick\Form;

use Nick;
use Nick\Event\Event;
use Nick\Form\FormElements\Hidden;
use Nick\Logger;
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
  }

  /**
   * Returns form elements in array format and fires an event.
   *
   * @return string
   */
  public function result(): string {
    $build = $this->build();
    $event = new Event('FormAlter');
    $event->fire($build, ['form-' . $this->getId()]);
    $render = '<form method="post" name="form-' . $this->getId() . '">';
    $formIdElement = new Hidden();
    $render .= $formIdElement->render([
      'formId' => $this->getId(),
      'key' => 'form-id',
      'attributes' => [
        'name' => 'form-id',
        'value' => $this->getId(),
      ],
    ]);
    foreach ($build as $key => $element) {
      $element['key'] = $key;
      $element['formId'] = $this->getId();
      if ($key !== 'submit') {
        $element['attributes']['name'] = $key;
      }
      $element['attributes']['value'] = $element['attributes']['value'] ?? NULL;
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
   *
   * @return bool
   */
  public function submit(array &$form, string $formId): bool {
    // Fire FormPreSubmitAlter event
    $preSubmitEvent = new Event('FormPreSubmitAlter');
    $preSubmitEvent->fire($form, [$formId]);

    // Fire submit handler
    if (!$handler = $form['submit']['form']['handler']) {
      return FALSE;
    }
    $handlerClass = new $handler[0];

    try {
      // Attempt to call the submit handler
      $handlerClass->{$handler[1]}($form, $_POST);
    } catch(\Exception $e) {
      Nick::Logger()->add($e->getMessage(), Logger::TYPE_ERROR, 'Form Submit');
    }

    // Fire FormPostSubmitAlter event
    $postSubmitEvent = new Event('FormPostSubmitAlter');
    $postSubmitEvent->fire($form, [$formId]);
    return TRUE;
  }

  /**
   * Default submit handler does nothing because there is nothing to handle!
   */
  public function submitForm() {

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