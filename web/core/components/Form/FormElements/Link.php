<?php

namespace Nick\Form\FormElements;

use Nick;
use Nick\Form\FormElement;

/**
 * Class Link
 *
 * @package Nick\Form\FormElements
 */
class Link implements FormElement {

  /**
   * {@inheritDoc}
   */
  public function render($variables = []) {
    return Nick::Renderer()->setType('core.Form')
      ->setTemplate('link')
      ->render($variables);
  }

}
