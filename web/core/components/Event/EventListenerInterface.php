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
   * postInit event
   *
   * @param array|null $cache
   *
   * @return mixed
   */
  public function postInit(?array &$cache);

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
   * addSearchResults event
   *
   * @param array|null $results
   * @param string     $keyword
   *
   * @return mixed
   */
  public function addSearchResults(?array &$results, string $keyword);

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

  /**
   * preRetrieve event
   *
   * @param array|null $information
   *
   * @return mixed
   */
  public function preRetrieve(?array $information = []);

  /**
   * postRetrieve event
   *
   * @param EntityInterface|null $data
   *
   * @return mixed
   */
  public function postRetrieve(?EntityInterface $data);

  /**
   * preRetrieve event
   *
   * @param array|null $information
   *
   * @return mixed
   */
  public function preTransmit(?array $information = []);

  /**
   * postRetrieve event
   * Gets fired before saving the entity to Nick
   *
   * @param EntityInterface|null $data
   *
   * @return mixed
   */
  public function postTransmit(?EntityInterface $data);

}
