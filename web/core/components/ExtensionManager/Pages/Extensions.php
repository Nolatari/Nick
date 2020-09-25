<?php

namespace Nick\ExtensionManager\Pages;

use Nick;
use Nick\Page\Page;
use Nick\ExtensionManager;

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
  public function render($parameters = []) {
    parent::render($parameters);
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
    if (isset($parameters['id'])) {
      $extension = $extensionManager::getExtensionInfo($parameters['id']);
      if (isset($parameters['t'])) {
        $action = $parameters['t'];
        if ($parameters['t'] == 'uninstall') {
          if (isset($parameters['confirm'])) { // @TODO: Change this to POST parameters.
            $extensionManager::uninstallExtension($parameters['id']);
            header('Location: ./?p=' . $this->get('id') . '&id=' . $parameters['id']);
          }
        } elseif ($parameters['t'] == 'install') {
          if (isset($parameters['confirm'])) { // @TODO: Change this to POST parameters.
            $extensionManager::installExtension($parameters['id'], $extension['type']);
            header('Location: ./?p=' . $this->get('id') . '&id=' . $parameters['id']);
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
        'active' => $parameters['id'] ?? FALSE,
        'action' => $action,
      ]);
  }

}
