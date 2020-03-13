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
   * @param array $variables
   *
   * @return mixed
   */
  public function fireEvent(&$variables = []);

}