<?php

namespace Nick\Form;

use Nick;
use Nick\Event\Event;
use Nick\Form\FormElements\Hidden;

/**
 * Class FormBuilder
 *
 * @package Nick
 */
class FormBuilder {

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

}