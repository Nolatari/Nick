<?php

namespace Nick\Events;

class Event extends FireEvent implements EventInterface {

  /** @var string $eventName */
  protected $eventName;

  /**
   * Event constructor.
   *
   * @param $eventName
   */
  public function __construct($eventName) {
    $this->setEventName($eventName);
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
  public function setEventName($eventName) {
    $this->eventName = $eventName;
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function fireEvent(&$variables = []) {
    $this->fire($this, $variables);
  }

}