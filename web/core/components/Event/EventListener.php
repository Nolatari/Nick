<?php

namespace Nick\Event;

use Nick\Entity\EntityInterface;

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
  public function postInit(?array &$cache) {}

  /**
   * {@inheritDoc}
   */
  public function pagePreRender(?array &$variables, string $page_id) {}

  /**
   * {@inheritDoc}
   */
  public function addSearchResults(?array &$results, string $keyword) {}

  /**
   * {@inheritDoc}
   */
  public function preRender(?array &$variables, ?string $view_mode) {}

  /**
   * {@inheritDoc}
   */
  public function FormAlter(?array &$form, string $form_id) {}

  /**
   * {@inheritDoc}
   */
  public function stringTranslationPresave(?array &$variables, ?string $string, ?array $args, ?string $from_langcode, ?string $to_langcode) {}

  /**
   * {@inheritDoc}
   */
  public function EntityPreDelete(?EntityInterface $entity) {}

  /**
   * {@inheritDoc}
   */
  public function EntityPostDelete(?EntityInterface $entity) {}

  /**
   * {@inheritDoc}
   */
  public function EntityPreSave(?EntityInterface $entity) {}

  /**
   * {@inheritDoc}
   */
  public function EntityPostSave(?EntityInterface $entity) {}

  /**
   * {@inheritDoc}
   */
  public function preRetrieve(?array $information = []) {}

  /**
   * {@inheritDoc}
   */
  public function postRetrieve(?EntityInterface $data) {}

  /**
   * {@inheritDoc}
   */
  public function preTransmit(?array $information = []) {}

  /**
   * {@inheritDoc}
   */
  public function postTransmit(?EntityInterface $data) {}

}
