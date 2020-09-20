<?php

namespace Nick\Form\FormElements;

use Nick\Form\FormElement;
use Nick;

/**
 * Class Select
 *
 * @package Nick\Form\FormElements
 */
class Select implements FormElement {

  /**
   * {@inheritDoc}
   */
  public function render($variables = []) {
    return Nick::Renderer()->setType('form_elements')
      ->setTemplate('select')
      ->render($variables);
  }

}
