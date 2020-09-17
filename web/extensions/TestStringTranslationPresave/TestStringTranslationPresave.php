<?php

namespace Nick\TestStringTranslationPresave;

use Nick\Form\FormStateInterface;

/**
 * Class TestStringTranslationPresave
 *
 * @package Nick\TestStringTranslationPresave
 */
class TestStringTranslationPresave {

  /**
   * Tests the FormAlter event.
   * Defined in the TestFormAlter.yml file!
   *
   * @param array       $variables
   * @param string|null $view_mode
   */
  public function TranslationPresave(array &$variables, $string, $args, $from_langcode, $to_langcode) {
    d($variables);
  }

}
