<?php

namespace Nick\Pages;

/**
 * Class Error
 *
 * @package Nick\Pages
 */
class Error extends Pages {

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
    parent::render();
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
    return \Nick::Renderer()->setType('error')->setTemplate($parameters['e'])->render();
  }

}