<?php

namespace Nick\Rest;

use Nick\Page\Page;

/**
 * Class RestPage
 * @package Nick\Rest
 */
class RestPage extends Page {

  /**
   * RestPage constructor.
   */
  public function __construct() {
    parent::__construct();
    \Nick::Request()->headers->set('Content-Type', 'application/json');
  }

}
