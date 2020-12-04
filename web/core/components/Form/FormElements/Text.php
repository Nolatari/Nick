<?php

namespace Nick\Form\FormElements;

use Nick;
use Nick\Form\FormElement;

/**
 * Class Text
 *
 * @package Nick\Form\FormElements
 */
class Text implements FormElement {

  /**
   * {@inheritDoc}
   */
  public function render($variables = []) {
    return Nick::Renderer()->setType('core.Form')
      ->setTemplate('text')
      ->render($variables);
  }

}
