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
   * @param array  $variables
   *
   * @return mixed
   */
  public function get($type, $variables = []) {
    // Fire an event so this can be altered
    $event = new Event('FormElementAlter');
    $event->fire($variables);

    if (method_exists(new static(), $type)) {
      //return self::$type($variables);
      // @todo: fix this :-(
    }

    //return self::render($type, $variables);
    // @todo: fix this :-(
  }

}