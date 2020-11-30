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

  /** @var RouteInterface $current */
  protected static RouteInterface $current;

  /** @var string $route */
  protected string $route;

  /** @var string $controller */
  protected string $controller;

  /** @var string $url */
  protected string $url;

  /** @var bool $rest */
  protected bool $rest = FALSE;

  /** @var array $parameters */
  protected array $parameters;

  /** @var array $values */
  protected array $values = [];

  /** @var Request $request */
  protected Request $request;

  /**
   * Route constructor.
   */
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
    $query = \Nick::Database()
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
    return $this->setValues($route, $result['controller'], unserialize($result['parameters']), $result['url'], $result['rest'] ?? FALSE);
  }

  /**
   * Sets static current property
   *
   * @param RouteInterface $route
   */
  public static function setCurrent(RouteInterface $route) {
    static::$current = $route;
  }

  /**
   * Returns current route
   *
   * @return RouteInterface
   */
  public static function getCurrent(): RouteInterface {
    return static::$current;
  }

  /**
   * @return null|PageInterface
   */
  public function getPageObject() {
    $controller = $this->getController();
    if (!class_exists($controller)) {
      return NULL;
    }
    $object = new $controller;
    if (!$object instanceof PageInterface) {
      return NULL;
    }

    return $object;
  }

  /**
   * Renders route object (if it complies with PageInterface).
   *
   * @return mixed|null
   */
  public function render() {
    $parameters = Url::getRefactoredParameters($this);
    return \Nick::Cache()->getContentData($this->getPageObject()->getCacheOptions(), $this->controller, 'render', [$parameters, $this]);
  }

  /**
   * {@inheritDoc}
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
      case 'rest':
        $this->rest = $value;
        break;
      default:
        $this->values[$key] = $value;
        break;
    }
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function setValues(string $route, string $controller, array $parameters, string $url, bool $rest = FALSE): self {
    $this->route = $route;
    $this->controller = $controller;
    $this->parameters = $parameters;
    $this->url = $url;
    $this->rest = $rest;

    return $this;
  }

  /**
   * @param string $key
   *
   * @return mixed|null
   */
  public function getValue(string $key) {
    return $this->values[$key] ?? NULL;
  }

  /**
   * Returns route values in array format.
   *
   * @return array
   */
  public function getValues(): array {
    return $this->values;
  }

  /**
   * Returns current route
   *
   * @return string
   */
  public function getRoute() {
    return $this->route;
  }

  /**
   * Returns whether route is a rest call
   *
   * @return bool
   */
  public function isRest() {
    return $this->rest;
  }

  /**
   * Returns current route
   *
   * @return string
   */
  public function getController() {
    return $this->controller;
  }

  /**
   * Returns parameters in array format of current route.
   *
   * @return array
   */
  public function getParameters() {
    return $this->parameters;
  }

  /**
   * Returns URI from Request and current Route
   *
   * @param bool $replaceParams
   *
   * @return string|string[]
   */
  public function getUri($replaceParams = true) {
    $url = $this->url;
    if (!$replaceParams) {
      return $url;
    }
    $current_params = [];
    foreach ($this->values as $param => $value) {
      if (isset($this->parameters[$param])) {
        $url = StringManipulation::replace($url, '{' . $param . '}', $this->values[$param]);
      } else {
        $url = \Nick::Url()->addParamsToUrl($param, $value, $url, $current_params);
        $current_params[$param] = $value;
      }
    }
    return $url;
  }

  /**
   * Shorthand function to check whether route exists.
   *
   * @param $route
   *
   * @return bool
   */
  public function routeExists($route) {
    $route = $this->load($route);
    return $route !== FALSE;
  }

  /**
   * Saves route to database (Insert / update depending on current status)
   */
  public function save() {
    if ($this->routeExists($this->route)) {
      $query = \Nick::Database()
        ->update('routes')
        ->condition('route', $this->route)
        ->values([
          'controller' => $this->controller,
          'parameters' => serialize($this->parameters),
          'url' => $this->url,
        ]);
      return $query->execute();
    }

    $query = \Nick::Database()
      ->insert('routes')
      ->values([
        'route' => $this->route,
        'controller' => $this->controller,
        'parameters' => serialize($this->parameters),
        'url' => $this->url,
      ]);
    return $query->execute();
  }

}