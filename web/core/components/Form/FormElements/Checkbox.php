<?php

namespace Nick\Form\FormElements;

use Nick\Form\FormElement;
use Nick\Renderer;
use Nick;

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
