<?php

namespace Nick\Form\FormElements;

use Nick;
use Nick\Form\FormElement;

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
    return Nick::Renderer()->setType('core.Form')
      ->setTemplate('textbox')
      ->render($variables);
  }

}
