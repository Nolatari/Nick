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
    $this->cloneParameter('t', 'type');
    if ($this->get('export') !== NULL) {
      Nick::Config()->export();
    } elseif ($this->get('import') !== NULL) {
      Nick::Config()->import();
    } elseif ($this->get('difference') !== NULL) {
      // @TODO
    } else {
      switch ($this->get('type')) {
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