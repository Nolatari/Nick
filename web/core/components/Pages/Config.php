<?php

namespace Nick\Pages;

/**
 * Class Config
 *
 * @package Nick\Pages
 */
class Config extends Pages {

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions() {
    $this->caching = [
      'key' => 'page.config',
      'context' => 'page',
      'max-age' => 3600,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    parent::render();
    if (isset($_GET['export'])) {
      \Nick::Config()->export();
    } elseif (isset($_GET['import'])) {
      \Nick::Config()->import();
    } elseif (isset($_GET['difference'])) {
      // @TODO
    } else {
      switch ($_GET['t']) {
        case 'site':
          break;
        case 'appearance':
          break;
        default:
          break;
      }
    }
  }

}