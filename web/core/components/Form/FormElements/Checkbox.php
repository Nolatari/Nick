<?php

namespace Nick\Form\FormElements;

use Nick;
use Nick\Form\FormElement;

/**
 * Class Checkbox
 *
 * @package Nick\Form\FormElements
 */
class Checkbox implements FormElement {

  /**
   * {@inheritDoc}
   */
  public function render($variables = []) {
    return Nick::Renderer()->setType('form_elements')
      ->setTemplate('checkbox')
      ->render($variables);
  }

}
