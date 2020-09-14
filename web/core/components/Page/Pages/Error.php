<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Page;

/**
 * Class Error
 *
 * @package Nick\Page
 */
class Error extends Page {

  /**
   * Error constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->setParameters([
      'title' => $this->translate('Error'),
      'summary' => $this->translate('There was an error trying to reach a certain page.'),
    ]);
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions() {
    $this->caching = [
      'key' => 'page.error',
      'context' => 'page',
      'max-age' => -1,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render($parameters = []) {
    switch ($parameters['e']) {
      case '404':
        $title = 'Page not found';
        break;
      case '403':
        $title = 'Forbidden';
        break;
      case '301':
        $title = 'Moved permanently';
        break;
      default:
        $_GET['e'] = '500';
        $title = 'Internal server error';
        break;
    }

    $variables = $variables ?? [];
    $variables['page']['title'] = $title;
    return Nick::Renderer()
      ->setType('error')
      ->setTemplate($parameters['e'])
      ->render();
  }

}