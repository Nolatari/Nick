<?php

namespace Nick\Form\FormElements;

use Nick\Form\FormElement;
use Nick\Renderer;
use Nick;

/**
 * Class Button
 *
 * @package Nick\Form\FormElements
 */
class Button implements FormElement {

  /**
   * {@inheritDoc}
   */
  public function render($variables = []) {
    return Nick::Renderer()->setType('form_elements')
      ->setTemplate('button')
      ->render($variables);
  }

}
