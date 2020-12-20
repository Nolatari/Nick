<?php

namespace Nick\Entity\Pages;

use Nick;
use Nick\Page\Page;
use Nick\Route\RouteInterface;
use Nick\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Update
 *
 * @package Nick\Entity\Pages
 */
class Update extends Page {

  /**
   * Update constructor.
   */
  public function __construct(array &$parameters, RouteInterface $route) {
    $this->setParameters([
      'id' => 'entity.update',
      'title' => $this->translate('Entity update'),
      'summary' => $this->translate('Update entities.'),
    ]);
    parent::__construct($parameters, $route);
  }

  /**
   * {@inheritDoc}
   */
  public function setCacheOptions(): self {
    $this->caching = [
      'key' => 'page.entity.update',
      'context' => 'page',
      'max-age' => 0,
    ];

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function render() {
    parent::render();

    \Nick::EntityManager()->updateEntities();

    $dashboard = \Nick::Route()->load('dashboard');
    $response = new RedirectResponse(Url::fromRoute($dashboard));
    $response->send();
  }

}
