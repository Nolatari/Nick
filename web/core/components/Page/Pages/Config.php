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
  public function render($parameters = []) {
    $this->setParameter('type', $parameters['t'] ?? '');
    if (isset($parameters['export'])) {
      Nick::Config()->export();
    } elseif (isset($parameters['import'])) {
      Nick::Config()->import();
    } elseif (isset($parameters['difference'])) {
      // @TODO
    } else {
      switch ($_GET['t']) {
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