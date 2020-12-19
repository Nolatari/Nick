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
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'entity.overview',
      'title' => $this->translate('Entity overview'),
      'summary' => $this->translate('List of existing entities.'),
    ]);
    parent::__construct($parameters, $route);
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions(): self {
    $this->caching = [
      'key' => 'page.entity.overview',
      'context' => 'page',
      'tags' => [],
      'max-age' => 900,
    ];

    if ($this->hasParameter('type')) {
      $this->caching['key'] = 'page.entity.' . $this->get('type') . '.overview';
      $this->caching['tags'] = ['entity:' . $this->get('type') . ':overview'];
    }

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    parent::render();

    $content = NULL;
    $manifest = \Nick::Manifest($this->get('type'))->fields([
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
          'content' => $content,
        ],
      ]);
  }

}
