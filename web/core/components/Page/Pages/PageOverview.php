<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Manifest\ManifestRenderer;
use Nick\Page\Page;

/**
 * Class PageOverview
 *
 * @package Nick\Page\Pages
 */
class PageOverview extends Page {

  /**
   * PageOverview constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'pageoverview',
      'title' => $this->translate('Pages overview'),
      'summary' => $this->translate('Shows the list of active pages'),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.' . $this->get('id'),
      'context' => 'page',
      'max-age' => 300,
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function render(&$parameters = []) {
    $manifest = Nick::Manifest('page');
    $renderer = new ManifestRenderer($manifest);
  }

}
