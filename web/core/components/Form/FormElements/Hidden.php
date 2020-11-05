<?php

namespace Nick\Form\FormElements;

use Nick;
use Nick\Form\FormElement;

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
    return \Nick::Renderer()->setType('core.Form')
      ->setTemplate('hidden')
      ->render($variables);
  }

}
