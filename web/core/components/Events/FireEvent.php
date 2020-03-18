<?php

namespace Nick\Events;

use Nick\Core;
use Nick\Logger;

/**
 * Class FireEvent
 *
 * @package Nick\Events
 */
class FireEvent {

  /**
   * Fires the event and runs through all listeners/subscribers.
   *
   * @param EventInterface $event
   * @param array $variables
   * @param array $otherArgs
   *
   * @return bool
   */
  protected function fire(EventInterface $event, &$variables = [], $otherArgs = []) {
    foreach ($this->getListeners($event->getEventName()) as $listener) {
      $class = new $listener['class']();
      try {
        // Call the listener class' method
        $class->{$listener['method']}($variables, ...$otherArgs);
      } catch (\Exception $e) {
        \Nick::Logger()->add($e->getMessage(), Logger::TYPE_ERROR, 'EventListener');
        return FALSE;
      }
    }

    return TRUE;
  }

  /**
   * Returns array of classes and methods with listeners.
   *
   * @param $eventName
   *
   * @return array
   */
  protected function getListeners($eventName) {
    $extensions = Core::getInstalledExtensions();
    $listeners = [];
    foreach ($extensions as $extension) {
      // Skip if this extension has no info file.
      if (!$extInfo = Core::getExtensionInfo($extension['name'], $extension['type'])) {
        continue;
      }

      // Skip if this extension does not listen to this (or any) events
      if (!isset($extInfo['event_listeners'][$eventName])) {
        continue;
      }

      // Extension listens to this event, add to array
      $listeners[] = $extInfo['event_listeners'][$eventName];
    }

    return $listeners;
  }

}