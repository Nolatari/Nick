<?php

namespace Nick\Form\FormElements;

use Nick\Form\FormElement;
use Nick\Renderer;
use Nick;

/**
 * Class Textbox
 *
 * @package Nick\Form\FormElements
 */
class Textbox implements FormElement {

  /**
   * {@inheritDoc}
   */
  public function render($variables = []) {
    return Nick::Renderer()->setType('form_elements')
      ->setTemplate('textbox')
      ->render($variables);
  }

}
