<?php

namespace Nick\Event;

use Nick\Entity\EntityInterface;

/**
 * interface EventListenerInterface
 *
 * @package Nick\Event
 */
interface EventListenerInterface {

  /**
   * pagePreRender event
   *
   * @param array|null $variables
   * @param string     $page_id
   *
   * @return mixed
   */
  public function pagePreRender(?array &$variables, string $page_id);

  /**
   * preSearchRender event
   *
   * @param array|null $results
   * @param string     $keyword
   *
   * @return mixed
   */
  public function preSearchRender(?array &$results, string $keyword);

  /**
   * preRender event
   *
   * @param array       $variables
   * @param string|null $view_mode
   */
  public function preRender(array &$variables, ?string $view_mode);

  /**
   * FormAlter event
   *
   * @param array  $form
   * @param string $form_id
   */
  public function FormAlter(array &$form, string $form_id);

  /**
   * stringTranslationPresave event
   *
   * @param array       $variables
   * @param string|null $string
   * @param array|null  $args
   * @param string|null $from_langcode
   * @param string|null $to_langcode
   */
  public function stringTranslationPresave(array &$variables, ?string $string, ?array $args, ?string $from_langcode, ?string $to_langcode);

  /**
   * EntityPreDelete event
   *
   * @param EntityInterface|null $entity
   */
  public function EntityPreDelete(?EntityInterface $entity);

  /**
   * EntityPostDelete event
   *
   * @param EntityInterface|null $entity
   */
  public function EntityPostDelete(?EntityInterface $entity);

  /**
   * EntityPreSave event
   *
   * @param EntityInterface|null $entity
   */
  public function EntityPreSave(?EntityInterface $entity);

  /**
   * EntityPostSave event
   *
   * @param EntityInterface|null $entity
   */
  public function EntityPostSave(?EntityInterface $entity);
}
