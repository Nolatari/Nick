<?php

namespace Nick\Form\FormElements;

use Nick;
use Nick\Form\FormElement;

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
    return \Nick::Renderer()->setType('core.Form')
      ->setTemplate('button')
      ->render($variables);
  }

}
