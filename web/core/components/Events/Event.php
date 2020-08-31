<?php

namespace Nick\Events;

use Exception;
use Nick;
use Nick\ExtensionManager;
use Nick\Logger;

/**
 * Class Event
 *
 * @package Nick\Events
 */
class Event implements EventInterface {

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

  /**
   * Fires the event and runs through all listeners/subscribers.
   *
   * @param array $variables
   * @param array $otherArgs
   *
   * @return bool
   */
  public function fire(&$variables = [], $otherArgs = []) {
    foreach ($this->getListeners() as $listener) {
      $class = new $listener['class']();
      try {
        if (!is_array($otherArgs)) {
          Nick::Logger()->add('The other arguments have to be of the array format.', Logger::TYPE_ERROR, 'EventListener');
          return FALSE;
        }
        // Call the listener class' method
        $class->{$listener['method']}($variables, ...$otherArgs);
      } catch (Exception $exception) {
        Nick::Logger()->add($exception->getMessage(), Logger::TYPE_ERROR, 'EventListener');
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * Returns array of classes and methods with listeners.
   *
   * @return array
   */
  protected function getListeners() {
    $extensions = ExtensionManager::getInstalledExtensions();
    $listeners = [];
    foreach ($extensions as $extension) {
      // Skip if this extension has no info file.
      if (!$extInfo = ExtensionManager::getExtensionInfo($extension['name'])) {
        continue;
      }

      // Skip if this extension does not listen to this (or any) events
      if (!isset($extInfo['event_listeners'][$this->getEventName()])) {
        continue;
      }

      // Extension listens to this event, add to array
      $listeners[] = $extInfo['event_listeners'][$this->getEventName()];
    }

    return $listeners;
  }

}