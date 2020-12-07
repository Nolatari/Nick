<?php

namespace Nick\Entity\Pages;

use Nick;
use Nick\Manifest\ManifestRenderer;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class Overview
 *
 * @package Nick\Entity\Pages
 */
class Overview extends Page {

  /**
   * Overview constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'entity.overview',
      'title' => $this->translate('Entity overview'),
      'summary' => $this->translate('List of existing entitys.'),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []): self {
    $this->caching = [
      'key' => 'page.entity.overview',
      'context' => 'page',
      'max-age' => 300,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);

    $content = NULL;
    $manifest = \Nick::Manifest($parameters[1])->fields([
      'id', 'title', 'status', 'owner'
    ]);
    $manifestRenderer = new ManifestRenderer($manifest);
    $content = $manifestRenderer
      ->setViewMode($parameters['viewmode'] ?? 'table')
      ->hideField('id')
      ->noLink('owner')
      ->addActionLinks('entity')
      ->render(TRUE);

    return \Nick::Renderer()
      ->setType('core.Entity')
      ->setTemplate('overview')
      ->render([
        'page' => [
          'id' => $this->get('id'),
          'title' => $this->get('title'),
          'summary' => $this->get('summary'),
        ],
        'entity' => [
          'id' => $parameters['id'] ?? NULL,
          'content' => $content,
        ],
      ]);
  }

}
