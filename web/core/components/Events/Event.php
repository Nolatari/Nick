<?php

namespace Nick\Events;

/**
 * Class Event
 *
 * @package Nick\Events
 */
class Event extends FireEvent implements EventInterface {

  /** @var string $eventName */
  protected $eventName;

  /**
   * Event constructor.
   *
   * @param string $eventName
   */
  public function __construct($eventName) {
    $this->setEventName($eventName);
  }

  /**
   * {@inheritDoc}
   */
  public function fireEvent(&$variables = [], $otherArgs = []) {
    return $this->fire($this, $variables, $otherArgs);
  }

  /**
   * {@inheritDoc}
   */
  public function getEventName() {
    return $this->eventName;
  }

  /**
   * {@inheritDoc}
   */
  protected function setEventName($eventName) {
    $this->eventName = $eventName;
    return $this;
  }

}