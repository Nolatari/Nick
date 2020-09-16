<?php

namespace Nick\Event;

interface EventInterface {

  /**
   * Returns the event's name.
   *
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