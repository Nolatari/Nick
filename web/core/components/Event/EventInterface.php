<?php

namespace Nick\Event;

interface EventInterface {

  /**
   * @return string
   */
  public function getEventName();

  /**
   * Fires event to trigger listeners/subscribers.
   *
   * @param mixed $variables
   * @param mixed $otherArgs
   *
   * @return bool
   */
  public function fire(&$variables = [], $otherArgs = []);

}