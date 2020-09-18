<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Page;
use Nick\ExtensionManager;

/**
 * Class Dashboard
 *
 * @package Nick\Page\Pages
 */
class Extensions extends Page {

  /**
   * Dashboard constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->setParameters([
      'id' => 'extensions',
      'title' => $this->translate('Extensions'),
      'summary' => $this->translate('Welcome to your Nick Dashboard!'),
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function install() {
    $pageManager = Nick::PageManager();
    return $pageManager->createPage([
      'id' => $this->get('id'),
      'controller' => '\\Nick\\Page\\Pages\\Extensions',
    ]);
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions() {
    $this->caching = [
      'key' => 'page.extensions',
      'context' => 'page',
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
    parent::render($parameters);
    $extensionManager = Nick::ExtensionManager();
    $extensionList = $extensionManager::getContribExtensions() + $extensionManager::getCoreExtensions();
    $extensions = [];
    foreach ($extensionList as $extension) {
      $extensions[$extension] = $extensionManager::getExtensionInfo($extension);
      $extensions[$extension]['installed'] = $extensionManager::extensionInstalled($extension);
      $extensions[$extension]['latest'] = FALSE; // TODO!
    }

    return Nick::Renderer()
      ->setType()
      ->setTemplate('extensions')
      ->render([
        'page' => [
          'id' => $this->get('id'),
          'title' => $this->get('title'),
          'summary' => $this->get('summary'),
        ],
        'extensions' => $extensions,
        'active' => $parameters['id'] ?? FALSE,
      ]);
  }

}
