<?php

namespace Nick\Form;

use Nick;
use Nick\Event\Event;
use Nick\Form\FormElements\Hidden;
use Nick\Url;

/**
 * Class FormBuilder
 *
 * @package Nick
 */
class FormBuilder {

  /** @var array $values */
  protected array $values;

  /**
   * Returns form elements in array format and fires an event.
   *
   * @return string
   */
  public function result(): string {
    $build = $this->build();
    $event = new Event('FormAlter');
    $event->fire($build, ['form-' . $this->getId()]);
    $render = '<form method="post" action="' . Url::fromRoute(Nick::Route()->load('form.submit')) . '" name="form-' . $this->getId() . '">';
    $formIdElement = new Hidden();
    $render .= $formIdElement->render([
      'formId' => $this->getId(),
      'key' => 'form-array',
      'attributes' => [
        'name' => 'form-array',
        'value' => htmlspecialchars(serialize($this->getFields())),
      ],
    ]);
    foreach ($build as $key => $element) {
      $element['key'] = $key;
      $element['formId'] = $this->getId();
      if ($key !== 'submit') {
        $element['attributes']['name'] = $key;
      }
      $element['attributes']['value'] = $this->getValue($key) ?? NULL;
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
   * Returns array of fields.
   *
   * @return null|array
   */
  protected function getFields(): ?array {
    return NULL;
  }

  /**
   * Returns Form ID.
   *
   * @return null|string
   */
  protected function getId(): ?string {
    return NULL;
  }

  /**
   * @param string $key
   *
   * @return string|null
   */
  protected function getValue(string $key): ?string {
    return NULL;
  }

}