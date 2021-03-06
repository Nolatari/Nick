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
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'extensions',
      'title' => $this->translate('Extensions'),
      'summary' => $this->translate('Welcome to your Nick Dashboard!'),
    ]);
    parent::__construct($parameters, $route);
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
  public function setCacheOptions(): self {
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
  public function render() {
    parent::render();

    $extensionManager = \Nick::ExtensionManager();
    $componentList = $extensionManager::getCoreComponents();
    $extensionList = array_merge($extensionManager::getContribExtensions(), $extensionManager::getCoreExtensions());
    $extensions = [];
    foreach ($extensionList as $extension) {
      $extensions[$extension] = $extensionManager::getExtensionInfo($extension);
      $extensions[$extension]['installed'] = $extensionManager::extensionInstalled($extension);
      $extensions[$extension]['latest'] = $extensionManager::isLatestVersion($extension); // TODO!
    }
    ksort($extensions);
    $extensions = ['core' => [
        'type' => 'core',
        'name' => 'Nick Core',
        'version' => \Nick::Cache()->getData('NICK_VERSION') . '.'
          . \Nick::Cache()->getData('NICK_VERSION_RELEASE') . '.'
          . \Nick::Cache()->getData('NICK_VERSION_RELEASE_MINOR') . ' '
          . \Nick::Cache()->getData('NICK_VERSION_STATUS'),
        'latest' => $extensionManager::isLatestVersion('core'),
        'installed' => TRUE,
        'required' => TRUE,
        'description' => "Includes the following components: \n" . implode("\n", $componentList),
      ],
    ] + $extensions;

    $action = NULL;
    if ($this->hasParameter('ext')) {
      $extension = $extensionManager::getExtensionInfo($this->get('ext'));
      if ($this->hasParameter('action')) {
        $action = $this->get(2);
        if ($this->get(2) == 'uninstall') {
          if ($this->hasParameter(3) && StringManipulation::contains($this->get(3), 'confirm')) {
            $extensionManager::uninstallExtension($this->get('ext'));
            $response = new RedirectResponse(Url::fromRoute(\Nick::Route()->load('extension.view')->setValue('ext', $this->get('ext'))));
            $response->send();
          }
        } elseif ($this->get(2) == 'install') {
          if ($this->hasParameter(3) && StringManipulation::contains($this->get(3), 'confirm')) {
            $extensionManager::installExtension($this->get('ext'), $extension['type']);
            $response = new RedirectResponse(Url::fromRoute(\Nick::Route()->load('extension.view')->setValue('ext', $this->get('ext'))));
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
        'active' => $this->get('ext') ?? FALSE,
        'action' => $action,
      ]);
  }

}
