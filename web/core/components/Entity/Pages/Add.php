<?php

namespace Nick\Entity\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class Add
 *
 * @package Nick\Entity\Pages
 */
class Add extends Page {

  /**
   * Edit constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'entity.add',
      'title' => $this->translate('Add entity'),
      'summary' => $this->translate('Add page for an entity'),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.entity.add',
      'context' => 'page',
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);

    $entity = \Nick::EntityManager()::getEntityClassFromType($parameters['type']);
    $form = \Nick::Form($entity);
    $content = $form->result();

    return \Nick::Renderer()
      ->setType('core.Entity')
      ->setTemplate('add')
      ->render([
        'page' => [
          'id' => $this->get('id'),
          'title' => $this->get('title'),
          'summary' => $this->get('summary'),
        ],
        'entity' => [
          'type' => $entity->getType(),
          'content' => $content,
        ],
      ]);
  }

}
