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
   * Creates route from string or array
   *
   * @param string|array $route
   *
   * @param array        $extra
   *
   * @return string
   */
  public function getRoute($route, $extra = []) {
    return Url::fromRoute($route, $extra);
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
