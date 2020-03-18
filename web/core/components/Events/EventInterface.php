<?php

namespace Nick\Events;

interface EventInterface {

  /**
   * @return string
   */
  public function getEventName();

  /**
   * Sets event name
   *
   * @param string $eventName
   *
   * @return self
   */
  public function setEventName($eventName);

  /**
   * Fires event to trigger listeners/subscribers.
   *
   * @param mixed $variables
   * @param mixed $otherArgs
   *
   * @return bool
   */
  public function fireEvent(&$variables = [], $otherArgs = []);

}