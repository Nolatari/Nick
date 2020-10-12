<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;
use Nick\Url;

/**
 * Class Logs
 *
 * @package Nick\Page
 */
class Logs extends Page {

  /**
   * Config constructor.
   */
  public function __construct() {
    $this->setParameters([
      'id' => 'logs',
      'title' => $this->translate('Logs'),
      'summary' => $this->translate('Shows recent logs'),
    ]);
    parent::__construct();
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []) {
    $this->caching = [
      'key' => 'page.logs',
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

    if ($route->getRoute() === 'logs.clear') {
      Nick::Logger()->clear();
      header('Location: ' . Url::fromRoute(Nick::Route()->load('logs')));
    }

    $logs = Nick::Logger()->getLogs(TRUE);
    return Nick::Renderer()
      ->setType()
      ->setTemplate('logs')
      ->render([
        'logs' => $logs,
        'count' => count($logs),
      ]);
  }

}