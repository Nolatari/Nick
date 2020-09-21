<?php

namespace Nick\TwigExtensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class Router
 *
 * @package App\Twig
 */
class Router extends AbstractExtension {

  /**
   * @return array|TwigFunction[]
   */
  public function getFunctions() {
    return [
      new TwigFunction('route', [$this, 'getRoute']),
    ];
  }

  /**
   * @param string|array $route
   *
   * @return string
   */
  public function getRoute($route) {
    if (!is_array($route)) {
      $route = explode('.', $route);
    }

    $returnString = './';
    if (isset($route[0])) {
      $returnString .= '?p=' . $route[0];
    }
    if (isset($route[1])) {
      $returnString .= '&t=' . $route[1];
    }
    if (isset($route[2])) {
      $returnString .= '&id=' . $route[2];
    }

    return $returnString;
  }

}
