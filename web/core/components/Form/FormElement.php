<?php

namespace Nick\Form;

/**
 * Class FormElement
 *
 * @package Nick\Form
 */
interface FormElement {

  /**
   * Renders Form Element.
   *
   * @param array $variables
   */
  public function render($variables = []);

}