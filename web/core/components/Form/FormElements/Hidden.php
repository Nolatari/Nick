<?php

namespace Nick\Form\FormElements;

use Nick\Form\FormElement;
use Nick\Renderer;
use Nick;

/**
 * Class Hidden
 *
 * @package Nick\Form\FormElements
 */
class Hidden implements FormElement {

  /**
   * {@inheritDoc}
   */
  public function render($variables = []) {
    return Nick::Renderer()->setType('form_elements')
      ->setTemplate('hidden')
      ->render($variables);
  }

}
