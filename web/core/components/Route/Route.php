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

  /**
   * @return null|PageInterface
   */
  public function getPageObject() {
    if (!class_exists($this->controller)) {
      return NULL;
    }
    $controller = new $this->controller;
    if (!$controller instanceof PageInterface) {
      return NULL;
    }

    return $controller;
  }

  /**
   * Renders route object (if it complies with PageInterface).
   *
   * @return mixed|null
   */
  public function render() {
    $parameters = Url::getRefactoredParameters($this);
    return Nick::Cache()->getContentData($this->getPageObject()->getCacheOptions(), $this->controller, 'render', [$parameters, $this]);
  }

  /**
   * Sets single value based on key.
   * Keys 'parameters', 'controller', 'route' and 'url' are taken by the system
   * Other keys will be added to the $values property.
   *
   * @param string $key
   *                  Key of the value to be set.
   * @param mixed  $value
   *                  The value to be set.
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
   *                  Route as string, e.g: person.edit
   * @param string $controller
   *                  Controller class as string, e.g:
   *                  \Nick\Person\Pages\Edit
   * @param array  $parameters
   *                  Array of reusable parameters (e.g: ['id' => 1])
   *                  where the integer 1 represents the place of the parameter
   *                  in the url. For example: /person/{id}/edit
   * @param string $url
   *                  The URL the route should link to.
   *                  Use reusable URLs here, e.g: /person/{id}/edit
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
   * Returns current route
   *
   * @return string
   */
  public function getRoute() {
    return $this->route;
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
        $urlObject = new Nick\Url();
        $url = $urlObject->addParamsToUrl($param, $this->values[$param], $url, $current_params);
        $current_params[$param] = $this->values[$param];
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
      $query = Nick::Database()
        ->update('routes')
        ->condition('route', $this->route)
        ->values([
          'controller' => $this->controller,
          'parameters' => serialize($this->parameters),
          'url' => $this->url,
        ]);
      return $query->execute();
    }

    $query = Nick::Database()
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