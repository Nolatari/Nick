<?php

namespace Nick\TestEvents;

use Nick\Event\EventListener;

/**
 * Class TestEvents
 *
 * @package Nick\TestEvents
 */
class TestEvents extends EventListener {

  /**
   * {@inheritDoc}
   */
  public function stringTranslationPresave(?array &$variables, $string, $args, $from_langcode, $to_langcode) {
    d($variables);
    d($string);
    d($args);
    d($from_langcode);
    d($to_langcode);
  }

  /**
   * {@inheritDoc}
   */
  public function FormAlter(?array &$form, string $form_id) {
    d($form);
    d($form_id);
  }

  /**
   * {@inheritDoc}
   */
  public function preRender(?array &$variables, ?string $view_mode) {
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
