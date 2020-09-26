<?php

namespace Nick\Form\FormElements;

use Nick\Form\FormElement;
use Nick;

/**
 * Class Wysiwyg
 *
 * @package Nick\Form\FormElements
 */
class Wysiwyg implements FormElement {

  /**
   * {@inheritDoc}
   */
  public function render($variables = []) {
    return Nick::Renderer()->setType('form_elements')
      ->setTemplate('wysiwyg')
      ->render($variables);
  }

}
