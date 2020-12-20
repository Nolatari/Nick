<?php

namespace Nick\Page\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;
use Nick\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Logs
 *
 * @package Nick\Page
 */
class Logs extends Page {

  /**
   * {@inheritDoc}
   */
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'logs',
      'title' => $this->translate('Logs'),
      'summary' => $this->translate('Shows recent logs'),
    ]);
    parent::__construct($parameters, $route);
  }

  /**
   * {@inheritDoc}
   */
  protected function setCacheOptions($parameters = []): self {
    $this->caching = [
      'key' => 'page.logs',
      'context' => 'page',
      'tags' => ['logs'],
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    parent::render();

    if ($this->getRoute()->getRoute() === 'logs.clear') {
      \Nick::Logger()->clear();
      $response = new RedirectResponse(Url::fromRoute(\Nick::Route()->load('logs')));
      $response->send();
    }

    $logs = \Nick::Logger()->getLogs(TRUE);
    return \Nick::Renderer()
      ->setType()
      ->setTemplate('logs')
      ->render([
        'logs' => $logs,
        'count' => count($logs),
      ]);
  }

}