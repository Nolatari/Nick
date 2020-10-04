<?php

namespace Nick\Event;

/**
 * Class EventListener
 *
 * Only dummy methods will be in here to provide a template for events in child classes.
 *
 * @package Nick\Event
 */
class EventListener implements EventListenerInterface {

  /**
   * {@inheritDoc}
   */
  public function pagePreRender(?array &$variables, string $page_id) {}

  /**
   * {@inheritDoc}
   */
  public function preSearchRender(?array &$results, string $keyword) {}

  /**
   * {@inheritDoc}
   */
  public function preRender(?array &$variables, ?string $view_mode) {}

  /**
   * {@inheritDoc}
   */
  public function FormAlter(?array &$form, string $form_id) {}

  /**
   * @inheritDoc
   */
  public function stringTranslationPresave(?array &$variables, ?string $string, ?array $args, ?string $from_langcode, ?string $to_langcode) {}

}
