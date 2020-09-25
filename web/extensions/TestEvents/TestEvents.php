<?php

namespace Nick\TestEvents;

/**
 * Class TestEvents
 *
 * @package Nick\TestEvents
 */
class TestEvents {

  /**
   * Tests the stringTranslationPresave event.
   * Defined in the TestEvents.yml file!
   *
   * @param array $variables
   * @param       $string
   * @param       $args
   * @param       $from_langcode
   * @param       $to_langcode
   */
  public function TranslationPresave(array &$variables, $string, $args, $from_langcode, $to_langcode) {
    d($variables);
    d($string);
    d($args);
    d($from_langcode);
    d($to_langcode);
  }

  /**
   * Tests the FormAlter event.
   * Defined in the TestEvents.yml file!
   *
   * @param array              $form
   * @param string             $form_id
   */
  public function FormAlter(array &$form, string $form_id) {
    d($form);
    d($form_id);
  }

  /**
   * Tests the preRender event.
   * Defined in the TestEvents.yml file!
   *
   * @param array       $variables
   * @param string|null $view_mode
   */
  public function preRender(array &$variables, $view_mode) {
    // Check whether page has an id (some don't!)
    if (!isset($variables['page']['id'])) {
      return;
    }

    // Check whether page ID matches the one we wish to alter
    if ($variables['page']['id'] != 'dashboard') {
      return;
    }

    // Change the page title to add a test message
    $variables['page']['title'] = $variables['page']['title'] . " - TEST!! Turn off the TestEvents module to remove this example.";
  }

}
