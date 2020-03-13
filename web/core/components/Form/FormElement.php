<?php

namespace Nick\Form;

use Nick\Events\Event;

/**
 * Class FormElement
 *
 * @package Nick\Form
 */
class FormElement {

  /**
   * @param string $type
   * @param array $variables
   *
   * @return mixed
   */
  public static function get($type, $variables = []) {
    if (!method_exists(new static(), $type)) {
      //return self::render($type, $variables);
      // @todo: fix this :-(
    }

    $event = new Event('FormElementAlter');
    $event->fireEvent($variables);

    return self::$type($variables);
  }

  /**
   * Adds
   *
   * @param array $variables
   * @param string $form
   *
   * @return string
   */
  protected static function addWrapper($variables, $form) {
    $return = '<button class="btn btn-secondary" type="button" data-toggle="collapse" data-target="#form_' . $variables['name'] . '" aria-expanded="false" aria-controls="form_' . $variables['name'] . '">
    ' . $variables['label'] . '
  </button>';
    $return .= '<fieldset class="collapse" id="form_' . $variables['name'] . '">';
    $return .= $form;
    $return .= '</fieldset>';
    return $return;
  }

}