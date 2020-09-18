<?php

namespace Nick\TestEvents;

use Nick\Form\FormStateInterface;

/**
 * Class TestEvents
 *
 * @package Nick\TestEvents
 */
class TestEvents {

  /**
   * Tests the stringTranslationPresave event.
   * Defined in the TestStringTranslationPresave.yml file!
   *
   * @param array $variables
   */
  public function TranslationPresave(array &$variables, $string, $args, $from_langcode, $to_langcode) {
    d($variables);
  }

  /**
   * Tests the FormAlter event.
   * Defined in the TestFormAlter.yml file!
   *
   * @param array              $form
   * @param string             $form_id
   * @param FormStateInterface $formState
   */
  public function FormAlter(array &$form, string $form_id, FormStateInterface $formState) {
    d($form);
    d($form_id);
    d($formState);
  }

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

    $variables['page']['title'] = $variables['page']['title'] . " - TEST!! Turn off the TestEvents module to remove this example.";
  }

}
