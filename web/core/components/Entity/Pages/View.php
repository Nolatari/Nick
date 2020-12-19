<?php

namespace Nick\Entity\Pages;

use Nick;
use Nick\Entity\EntityInterface;
use Nick\Entity\EntityRenderer;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class View
 *
 * @package Nick\Entity\Pages
 */
class View extends Page {

  /**
   * View constructor.
   */
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'entity.view',
      'title' => $this->translate('Entity'),
      'summary' => $this->translate('The view page of an entity.'),
    ]);
    parent::__construct($parameters, $route);
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions(): self {
    $this->caching = [
      'key' => 'page.entity.view',
      'context' => 'page',
      'max-age' => 0,
    ];

    if ($this->hasParameter('eid') && !empty($this->get('eid'))) {
      /** @var EntityInterface $entityObject */
      $entityObject = \Nick::EntityManager()::getEntityClassFromType($this->get('type'));
      if (!$entityObject instanceof EntityInterface) {
        return $this;
      }
      /** @var EntityInterface $entity */
      $entity = $entityObject::load($this->get('eid'));
      if (method_exists($entity, 'getTitle')) {
        $title = $entity->getTitle();
      } elseif (method_exists($entity, 'getName')) {
        $title = $entity->getName();
      } else {
        $title = $entity->getType();
      }
      $this->setParameter('title', $title);
      $this->caching['key'] = $this->caching['key'] . '.' . $entity->id();
      $this->caching['tags'] = ['entity:' . $entity->getType() . ':' . $entity->id()];
      $this->caching['max-age'] = 1800;
    }

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function install() {
    $pageManager = \Nick::PageManager();
    return $pageManager->createPage([
      'id' => $this->get('id'),
      'controller' => '\\Nick\\Entity\\Pages\\View',
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    parent::render();

    $content = NULL;
    if ($this->hasParameter('eid') && !empty($this->get('eid'))) {
      /** @var EntityInterface $entityObject */
      $entityObject = \Nick::EntityManager()::getEntityClassFromType($this->get('type'));
      if (!$entityObject instanceof EntityInterface) {
        return $this;
      }
      $entity = $entityObject::load($this->get('eid'));
      $entityRenderer = new EntityRenderer($entity);
      $content = $entityRenderer->render();

      return \Nick::Renderer()
        ->setType('core.Entity')
        ->setTemplate('view')
        ->render([
          'page' => [
            'id' => $this->get('id'),
            'title' => $this->get('title'),
            'summary' => $this->get('summary'),
          ],
          'entity' => [
            'id' => $this->hasParameter('eid') ? $this->get('eid') : NULL,
            'type' => isset($entity) ? $entity->getType() : NULL,
            'content' => $content,
          ],
        ]);
    }

    return NULL;
  }

}
