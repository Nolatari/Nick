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
      new TwigFunction('formElement', [$this, 'getFormElement'], ['is_safe' => ['html']]),
      new TwigFunction('trans', [$this, 'getTranslation']),
      new TwigFunction('getEnv', [$this, 'getEnv']),
      new TwigFunction('element', [$this, 'element'], ['is_safe' => ['html']]),
    ];
  }

  /**
   * Returns element content without having to add |raw to it (cleaner twig code)
   *
   * @param string|null $content
   *
   * @return null|string
   */
  public function element(?string $content): ?string {
    return $content;
  }

  /**
   * Creates url from route
   *
   * @param string $route
   * @param array  $values
   *
   * @return null|string
   */
  public function getRoute(string $route, array $values = []): ?string {
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
   * Creates a new form element
   *
   * @param string $element
   * @param array  $variables
   *
   * @return null|string
   */
  public function getFormElement(string $element, array $variables = []): ?string {
    $formElementString = '\\Nick\\Form\\FormElements\\' . ucfirst($element);
    $formElement = new $formElementString;
    if (!$formElement instanceof FormElement) {
      return NULL;
    }

    return $formElement->render($variables);
  }

  /**
   * Translates string.
   *
   * @param string $string
   * @param array  $args
   *
   * @return mixed
   */
  public function getTranslation(string $string, $args = []) {
    return $this->translate($string, $args);
  }

  /**
   * Returns environment variable (if allowed!)
   *
   * @param string $key
   *
   * @return mixed
   */
  public function getEnv(string $key) {
    return Core::getEnv($key, FALSE);
  }

}
