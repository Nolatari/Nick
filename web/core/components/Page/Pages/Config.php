<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Page;

/**
 * Class Config
 *
 * @package Nick\Page
 */
class Config extends Page {

  /**
   * Config constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->setParameters([
      'title' => $this->translate('Config'),
      'summary' => $this->translate('Configuration options'),
    ]);
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions() {
    $this->caching = [
      'key' => 'page.config',
      'context' => 'page',
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function install() {
    $pageManager = Nick::PageManager();
    return $pageManager->createPage([
      'id' => $this->get('id'),
      'controller' => '\\Nick\\Page\\Pages\\Config',
    ]);
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
    parent::render($parameters);
    if (isset($parameters['export'])) {
      Nick::Config()->export();
    } elseif (isset($parameters['import'])) {
      Nick::Config()->import();
    } elseif (isset($parameters['difference'])) {
      // @TODO
    } else {
      switch ($parameters['t']) {
        case 'site':
          // @TODO
          break;
        case 'appearance':
          // @TODO
          break;
        default:
          break;
      }
    }
  }

}