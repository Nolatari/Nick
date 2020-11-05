<?php

namespace Nick\Form\FormElements;

use Nick;
use Nick\Form\FormElement;

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
    return \Nick::Renderer()->setType('core.Form')
      ->setTemplate('select')
      ->render($variables);
  }

}
