<?php

namespace Nick\ExtensionManager\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;

/**
 * Class Dashboard
 *
 * @package Nick\ExtensionManager\Pages
 */
class Extensions extends Page {

  /**
   * Dashboard constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'extensions',
      'title' => $this->translate('Extensions'),
      'summary' => $this->translate('Welcome to your Nick Dashboard!'),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  public function install() {
    $pageManager = Nick::PageManager();
    return $pageManager->createPage([
      'id' => $this->get('id'),
      'controller' => '\\Nick\\ExtensionManager\\Pages\\Extensions',
    ]);
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []) {
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
  public function render(array &$parameters, RouteInterface $route) {
    parent::render($parameters, $route);
    $extensionManager = Nick::ExtensionManager();
    $extensionList = array_merge($extensionManager::getContribExtensions(), $extensionManager::getCoreExtensions());
    $extensions = [];
    foreach ($extensionList as $extension) {
      $extensions[$extension] = $extensionManager::getExtensionInfo($extension);
      $extensions[$extension]['installed'] = $extensionManager::extensionInstalled($extension);
      $extensions[$extension]['latest'] = $extensionManager::isLatestVersion($extension); // TODO!
    }
    ksort($extensions);

    $action = NULL;
    if (isset($parameters[1])) {
      $extension = $extensionManager::getExtensionInfo($parameters[1]);
      if (isset($parameters[2])) {
        $action = $parameters[2];
        if ($parameters[2] == 'uninstall') {
          if (isset($parameters['confirm'])) { // @TODO: Change this to POST parameters.
            $extensionManager::uninstallExtension($parameters[1]);
            header('Location: ./?p=' . $this->get('id') . '&id=' . $parameters[1]);
          }
        } elseif ($parameters[2] == 'install') {
          if (isset($parameters['confirm'])) { // @TODO: Change this to POST parameters.
            $extensionManager::installExtension($parameters[1], $extension['type']);
            header('Location: ./?p=' . $this->get('id') . '&id=' . $parameters[1]);
          }
        }
      }
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
        'active' => $parameters[1] ?? FALSE,
        'action' => $action,
      ]);
  }

}
