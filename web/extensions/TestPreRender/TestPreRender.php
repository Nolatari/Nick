<?php

namespace Nick\TestPreRender;

use Nick\Form\FormStateInterface;

/**
 * Class TestFormAlter
 *
 * @package Nick\TestFormAlter
 */
class TestPreRender {

  /**
   * Tests the FormAlter event.
   * Defined in the TestFormAlter.yml file!
   *
   * @param array       $variables
   * @param string|null $view_mode
   */
  public function preRender(array &$variables, $view_mode) {
    if (!isset($variables['page']['id'])) {
      return;
    }
    if ($variables['page']['id'] != 'dashboard') {
      return;
    }

    $variables['page']['title'] = $variables['page']['title'] . " - TEST!! Turn off the TestPreRender module to remove this example.";
  }

}
