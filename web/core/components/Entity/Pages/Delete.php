<?php

namespace Nick\Entity\Pages;

use Nick;
use Nick\Entity\EntityInterface;
use Nick\Page\Page;
use Nick\Route\RouteInterface;
use Nick\Url;

/**
 * Class Delete
 *
 * @package Nick\Entity\Pages
 */
class Delete extends Page {

  /**
   * Entity constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'entity.delete',
      'title' => $this->translate('Entity Delete'),
      'summary' => $this->translate('Delete page for an entity'),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.entity.delete',
      'context' => 'page',
      'max-age' => 0,
    ];

    if (isset($parameters[2]) && !empty($parameters[2])) {
      /** @var EntityInterface $entityObject */
      $entityObject = \Nick::EntityManager()::getEntityClassFromType($parameters[1]);
      if (!$entityObject instanceof EntityInterface) {
        return $this;
      }
      $entity = $entityObject::load($parameters[2]);
      $this->setParameter('title', $this->translate('Delete :title', [':title' => $entity->getTitle()]));
      $this->caching['key'] = $this->caching['key'] . '.' . $entity->getType() . '.' . $entity->id();
      $this->caching['max-age'] = 0;
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
      'controller' => '\\Nick\\Entity\\Pages\\Entity',
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);

    $content = NULL;
    if (isset($parameters[2]) && !empty($parameters[2])) {
      /** @var EntityInterface $entityObject */
      $entityObject = \Nick::EntityManager()::getEntityClassFromType($parameters[1]);
      if (!$entityObject instanceof EntityInterface) {
        return NULL;
      }
      $entity = $entityObject::load($parameters[2]);

      $content = 'Are you sure you wish to delete this ' . $entity->getTitle() . '? <br />';
      $content .= '<a class="btn btn-primary" href="' . Url::fromRoute(\Nick::Route()->load('entity.delete')->setValue('id', $parameters[2])->setValue('confirm', '')) . '">Yes, I\'m sure</a> ';
      $content .= '<a class="btn btn-danger" href="' . Url::fromRoute(\Nick::Route()->load('entity.view')->setValue('id', $parameters[2])) . '">No, take me back</a>';
    }

    return \Nick::Renderer()
      ->setType('core.Entity')
      ->setTemplate('delete')
      ->render([
        'page' => [
          'id' => $this->get('id'),
          'title' => $this->get('title'),
          'summary' => $this->get('summary'),
        ],
        'entity' => [
          'id' => $parameters[2] ?? NULL,
          'type' => isset($entity) ? $entity->getType() : NULL,
          'content' => $content,
        ],
      ]);
  }

}
