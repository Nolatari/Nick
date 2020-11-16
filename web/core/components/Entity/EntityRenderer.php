<?php

namespace Nick\Entity;

use Nick\Renderer;

/**
 * Class EntityRenderer
 *
 * @package Nick\Entity
 */
class EntityRenderer {

  /** @var EntityInterface $entity */
  protected EntityInterface $entity;

  /** @var Renderer $renderer */
  protected Renderer $renderer;

  /**
   * EntityRenderer constructor.
   *
   * @param EntityInterface $entity
   */
  public function __construct(EntityInterface $entity) {
    $this->setEntity($entity);
    $this->setRenderer();
  }

  /**
   * Returns Entity object
   *
   * @return EntityInterface
   */
  protected function getEntity(): EntityInterface {
    return $this->entity;
  }

  /**
   * Sets Entity object
   *
   * @param EntityInterface $entity
   *
   * @return self
   */
  protected function setEntity(EntityInterface $entity): self {
    $this->entity = $entity;
    return $this;
  }

  /**
   * Returns Renderer object
   *
   * @return Renderer
   */
  protected function getRenderer() {
    return $this->renderer;
  }

  /**
   * Sets Renderer object
   *
   * @return $this
   */
  protected function setRenderer(): self {
    $this->renderer = new Renderer();
    return $this;
  }

  /**
   * Returns rendered Entity object
   *
   * @param array       $variables
   * @param string|null $viewMode
   *
   * @return string|NULL
   */
  public function render(array $variables = [], $viewMode = NULL): ?string {
    $entity = $this->getEntity();
    $viewMode = $viewMode ?? 'default';
    $values = $entity->getValues();
    $values = array_merge($values, $variables);

    foreach ($values as $key => $value) {
      $values[$key] = htmlspecialchars_decode($value);
    }
    d($values);
    return $this
      ->getRenderer()
      ->setType('entity/' . $entity->getType())
      ->setTemplate($viewMode)
      ->render($values);
  }

}