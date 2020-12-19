<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Page as PageBase;
use Nick\Route\RouteInterface;

/**
 * Class Page
 *
 * @package Nick\Page\Pages
 */
class Page extends PageBase {

  /**
   * {@inheritDoc}
   */
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'page',
      'title' => $this->translate('page'),
      'summary' => $this->translate('Welcome to your Nick Dashboard!'),
    ]);
    parent::__construct($parameters, $route);
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []): self {
    $this->caching = [
      'key' => 'page',
      'context' => 'page',
      'tags' => ['page'],
      'max-age' => 1800,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    parent::render();

    $elementManager = \Nick::ElementManager();
    $this->addElement($elementManager->getElementObject('header'));
    // TODO: Turn this into the current route's element
    //$this->addElement($elementManager->getElementObject('header'));
    $this->addElement($elementManager->getElementObject('footer'));

    d($this->getElements());
    d($this->getRenderedElements());

    return \Nick::Renderer()
      ->setType()
      ->setTemplate('dashboard')
      ->render([
        'page' => [
          'id' => $this->get('id'),
          'title' => $this->get('title'),
          'summary' => $this->get('summary'),
        ],
        'elements' => $this->getRenderedElements(),
        'dashboard' => [
          'installedExtensions' => count(\Nick::ExtensionManager()::getInstalledExtensions()),
        ],
      ]);
  }

}