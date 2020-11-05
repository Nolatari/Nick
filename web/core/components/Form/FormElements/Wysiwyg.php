<?php

namespace Nick\Form\FormElements;

use Nick;
use Nick\Form\FormElement;

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
    return \Nick::Renderer()->setType('core.Form')
      ->setTemplate('wysiwyg')
      ->render($variables);
  }

}
