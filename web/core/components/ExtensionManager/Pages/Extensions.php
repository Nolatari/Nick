<?php

namespace Nick\ExtensionManager\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;
use Nick\StringManipulation;
use Nick\Url;

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
    if (isset($parameters[2])) {
      $extension = $extensionManager::getExtensionInfo($parameters[2]);
      if (isset($parameters[3])) {
        $action = $parameters[3];
        if ($parameters[3] == 'uninstall') {
          if (isset($parameters[4]) && StringManipulation::contains($parameters[4], 'confirm')) { // @TODO: Change this to POST parameters.
            $extensionManager::uninstallExtension($parameters[2]);
            $redirect = Url::fromRoute(Nick::Route()->load('extension.view')->setValue('ext', $parameters[2]));
            header('Location: ' . $redirect);
          }
        } elseif ($parameters[3] == 'install') {
          if (isset($parameters[4]) && StringManipulation::contains($parameters[4], 'confirm')) { // @TODO: Change this to POST parameters.
            $extensionManager::installExtension($parameters[2], $extension['type']);
            $redirect = Url::fromRoute(Nick::Route()->load('extension.view')->setValue('ext', $parameters[2]));
            header('Location: ' . $redirect);
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
        'active' => $parameters[2] ?? FALSE,
        'action' => $action,
      ]);
  }

}
