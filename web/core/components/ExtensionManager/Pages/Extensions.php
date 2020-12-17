<?php

namespace Nick\ExtensionManager\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;
use Nick\StringManipulation;
use Nick\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
    $pageManager = \Nick::PageManager();
    return $pageManager->createPage([
      'id' => $this->get('id'),
      'controller' => '\\Nick\\ExtensionManager\\Pages\\Extensions',
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []): self {
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
    $extensionManager = \Nick::ExtensionManager();
    $componentList = $extensionManager::getCoreComponents();
    $extensionList = array_merge($extensionManager::getContribExtensions(), $extensionManager::getCoreExtensions());
    $extensions = [];
    $extensions['core'] = [
      'type' => 'core',
      'name' => 'Nick Core',
      'version' => \Nick::Cache()->getData('NICK_VERSION') . '.'
      . \Nick::Cache()->getData('NICK_VERSION_RELEASE') . '.'
      . \Nick::Cache()->getData('NICK_VERSION_RELEASE_MINOR'). ' '
      . \Nick::Cache()->getData('NICK_VERSION_STATUS'),
      'installed' => TRUE,
      'required' => TRUE,
      'description' => "Includes the following components: \n" . implode("\n", $componentList),
    ];
    foreach ($extensionList as $extension) {
      $extensions[$extension] = $extensionManager::getExtensionInfo($extension);
      $extensions[$extension]['installed'] = $extensionManager::extensionInstalled($extension);
      $extensions[$extension]['latest'] = $extensionManager::isLatestVersion($extension); // TODO!
    }
    ksort($extensions);

    $action = NULL;
    if (isset($parameters['ext'])) {
      $extension = $extensionManager::getExtensionInfo($parameters['ext']);
      if (isset($parameters[2])) {
        $action = $parameters[2];
        if ($parameters[2] == 'uninstall') {
          if (isset($parameters[3]) && StringManipulation::contains($parameters[3], 'confirm')) { // @TODO: Change this to POST parameters.
            $extensionManager::uninstallExtension($parameters['ext']);
            $response = new RedirectResponse(Url::fromRoute(\Nick::Route()->load('extension.view')->setValue('ext', $parameters['ext'])));
            $response->send();
          }
        } elseif ($parameters[2] == 'install') {
          if (isset($parameters[3]) && StringManipulation::contains($parameters[3], 'confirm')) { // @TODO: Change this to POST parameters.
            $extensionManager::installExtension($parameters['ext'], $extension['type']);
            $response = new RedirectResponse(Url::fromRoute(\Nick::Route()->load('extension.view')->setValue('ext', $parameters['ext'])));
            $response->send();
          }
        }
      }
    }

    return \Nick::Renderer()
      ->setType()
      ->setTemplate('extensions')
      ->render([
        'page' => [
          'id' => $this->get('id'),
          'title' => $this->get('title'),
          'summary' => $this->get('summary'),
        ],
        'extensions' => $extensions,
        'active' => $parameters['ext'] ?? FALSE,
        'action' => $action,
      ]);
  }

}
