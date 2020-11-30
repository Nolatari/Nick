<?php

namespace Nick\Entity\Pages;

use Nick;
use Nick\Entity\EntityInterface;
use Nick\Form\Form;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class Edit
 *
 * @package Nick\Entity\Pages
 */
class Edit extends Page {

  /**
   * Entity constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'entity.edit',
      'title' => $this->translate('Entity edit'),
      'summary' => $this->translate('Edit page for an entity'),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.entity.edit',
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
      $this->setParameter('title', $this->translate('Edit :title', [':title' => $entity->getTitle()]));
      $this->caching['key'] = $this->caching['key'] . '.' . $entity->id();
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
        return $this;
      }
      $entity = $entityObject::load($parameters[2]);

      $form = new Form($entity);
      $content = $form->result();
    }

    return \Nick::Renderer()
      ->setType('core.Entity')
      ->setTemplate('edit')
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
