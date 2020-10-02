<?php

namespace Nick\Routing;

use Nick;
use Nick\Database\Result;
use Nick\StringManipulation;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Routing
 *
 * @package Nick\Routing
 */
class Routing {

  /** @var string $route */
  protected string $route;

  /** @var string $controller */
  protected string $controller;

  /** @var string $url */
  protected string $url;

  /** @var array $parameters */
  protected array $parameters;

  /**
   * @param $route
   *
   * @return bool|self
   */
  public function load($route) {
    $query = Nick::Database()
      ->select('routes')
      ->condition('route', $route)
      ->execute();
    if (!$query instanceof Result) {
      return FALSE;
    }
    $result = $query->fetchAllAssoc();
    return $this->setValues($route, $result['controller'], unserialize($result['parameters']));
  }

  /**
   * Sets all values at once
   *
   * @param string $route
   * @param string $controller
   * @param array  $parameters
   *
   * @return self
   */
  public function setValues(string $route, string $controller, array $parameters): self {
    $this->route = $route;
    $this->controller = $controller;
    $this->parameters = $parameters;

    return $this;
  }

  /**
   * Returns URI from Request and current Route
   *
   * @param Request $request
   */
  public function getUri(Request $request) {
    $queryParams = $request->query->all();
    if (count($this->parameters) > 0) {
      foreach ($this->parameters as $param) {
        $this->url = StringManipulation::replace($this->url, '{' . $param . '}', $queryParams[$param]);
      }
    }
  }

  /**
   * Saves route to database (Insert / update depending on current status)
   */
  public function save() {
    $query = Nick::Database()
      ->insert('routes')
      ->values([
        $this->route,
        $this->controller,
        serialize($this->parameters),
      ]);
    return $query->execute();
  }

}