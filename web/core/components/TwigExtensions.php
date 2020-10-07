<?php

namespace Nick;

use Nick\Form\FormElement;
use Nick\Translation\StringTranslation;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class TwigExtensions
 *
 * @package Nick\
 */
class TwigExtensions extends AbstractExtension {
  use StringTranslation;

  /**
   * @return array|TwigFunction[]
   */
  public function getFunctions() {
    return [
      new TwigFunction('route', [$this, 'getRoute']),
      new TwigFunction('formElement', [$this, 'getFormElement']),
      new TwigFunction('trans', [$this, 'trans']),
    ];
  }

  /**
   * Creates url from route
   *
   * @param string $route
   * @param array  $values
   *
   * @return string
   */
  public function getRoute(string $route, array $values = []) {
    $route = \Nick::Route()->load($route);
    if (!$route) {
      return NULL;
    }
    foreach ($values as $key => $value) {
      $route = $route->setValue($key, $value);
    }
    return Url::fromRoute($route);
  }

  /**
   * Translates string.
   *
   * @param string $string
   * @param array  $args
   *
   * @return mixed
   */
  public function trans(string $string, $args = []) {
    return $this->translate($string, $args);
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
