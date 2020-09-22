<?php

namespace Nick;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TwigExtensions
 *
 * @package Nick\
 */
class TwigExtensions extends AbstractExtension {

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
    return Url::fromRoute($route);
  }

}
