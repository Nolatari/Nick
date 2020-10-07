<?php

namespace Nick\Route;

use Nick;
use Nick\Database\Result;
use Nick\Page\PageInterface;
use Nick\StringManipulation;
use Nick\Url;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Route
 *
 * @package Nick\Route
 */
class Route implements RouteInterface {

  /** @var string $route */
  protected string $route;

  /** @var string $controller */
  protected string $controller;

  /** @var string $url */
  protected string $url;

  /** @var array $parameters */
  protected array $parameters;

  /** @var array $values */
  protected array $values = [];

  /** @var Request $request */
  protected Request $request;

  public function __construct() {
    $this->request = Request::createFromGlobals();
  }

  /**
   * Returns either bool or route object based on route string
   *
   * @param string $route
   *
   * @return bool|self
   */
  public function load(string $route) {
    $query = Nick::Database()
      ->select('routes')
      ->condition('route', $route)
      ->execute();
    if (!$query instanceof Result) {
      return FALSE;
    }
    $result = $query->fetchAllAssoc();
    $result = reset($result);
    if (!is_array($result)) {
      return FALSE;
    }
    return $this->setValues($route, $result['controller'], unserialize($result['parameters']), $result['url']);
  }

  public function render() {
    if (!class_exists($this->controller)) {
      return NULL;
    }
    $controller = new $this->controller;
    if (!$controller instanceof PageInterface) {
      return NULL;
    }

    $parameters = Url::getParameters();
    return $controller->render($parameters);
  }

  /**
   * Sets single value based on key.
   *
   * @param string $key
   * @param mixed  $value
   *
   * @return self
   */
  public function setValue(string $key, $value): self {
    switch ($key) {
      case 'parameters':
        $this->parameters = $value;
        break;
      case 'controller':
        $this->controller = $value;
        break;
      case 'route':
        $this->route = $value;
        break;
      case 'url':
        $this->url = $value;
        break;
      default:
        $this->values[$key] = $value;
        break;
    }
    return $this;
  }

  /**
   * Sets all values at once
   *
   * @param string $route
   * @param string $controller
   * @param array  $parameters
   * @param string $url
   *
   * @return self
   */
  public function setValues(string $route, string $controller, array $parameters, string $url): self {
    $this->route = $route;
    $this->controller = $controller;
    $this->parameters = $parameters;
    $this->url = $url;

    return $this;
  }

  /**
   * Returns URI from Request and current Route
   *
   * @return string|string[]
   */
  public function getUri() {
    $url = $this->url;
    foreach ($this->values as $param => $value) {
      if (isset($this->parameters[$param])) {
        $url = StringManipulation::replace($url, '{' . $param . '}', $this->values[$param]);
      } else {

      }
    }
    return $url;
  }

  /**
   * Saves route to database (Insert / update depending on current status)
   */
  public function save() {
    // TODO: update if route exists
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