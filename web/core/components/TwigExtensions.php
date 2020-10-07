<?php

namespace Nick;

use Nick\Form\FormElement;
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
      new TwigFunction('formElement', [$this, 'getFormElement']),
    ];
  }

  /**
   * Creates url from route
   *
   * @param string $route
   * @param array  $parameters
   *
   * @return string
   */
  public function getRoute(string $route, array $parameters = []) {
    $route = \Nick::Route()->load($route);
    if (!$route) {
      return NULL;
    }
    $route = $route->setValue('parameters', $parameters);
    return Url::fromRoute($route);
  }

  /**
   * Creates a new form element
   *
   * @param string $element
   * @param array  $variables
   *
   * @return null|string
   */
  public function getFormElement(string $element, array $variables = []) {
    $formElementString = '\\Nick\\Form\\FormElements\\' . ucfirst($element);
    $formElement = new $formElementString;
    if (!$formElement instanceof FormElement) {
      return NULL;
    }

    return $formElement->render($variables);
  }

}
