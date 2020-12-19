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
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'entity.edit',
      'title' => $this->translate('Entity edit'),
      'summary' => $this->translate('Edit page for an entity'),
    ]);
    parent::__construct($parameters, $route);
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions(): self {
    $this->caching = [
      'key' => 'page.entity.edit',
      'context' => 'page',
      'max-age' => 0,
    ];

    if ($this->hasParameter('eid') && !empty($this->get('eid'))) {
      /** @var EntityInterface $entityObject */
      $entityObject = \Nick::EntityManager()::getEntityClassFromType($this->get('type'));
      if (!$entityObject instanceof EntityInterface) {
        return $this;
      }
      $entity = $entityObject::load($this->get('eid'));
      $this->setParameter('title', $this->translate('Edit :title', [':title' => $entity->getTitle()]));
      $this->caching['key'] .= '.' . $entity->id();
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
      'controller' => '\\Nick\\Entity\\Pages\\Edit',
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
