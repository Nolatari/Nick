<?php

namespace Nick\Event;

use Exception;
use Nick;
use Nick\Logger;
use Nick\Translation\StringTranslation;

/**
 * Class Event
 *
 * @package Nick\Events
 */
class Event implements EventInterface {
  use StringTranslation;

  /** @var string $eventName */
  protected string $eventName;

  /**
   * Event constructor.
   *
   * @param string $eventName
   */
  public function __construct(string $eventName) {
    $this->setEventName($eventName);
  }

  /**
   * Fires the event and runs through all listeners/subscribers.
   *
   * @param mixed $variables
   * @param array $otherArgs
   *
   * @return bool
   */
  public function fire(&$variables = [], $otherArgs = []): bool {
    foreach ($this->getListeners() as $extension => $listener) {
      /** @var EventListenerInterface $class */
      $class = new $listener['class']();
      if (!$class instanceof EventListenerInterface) {
        Nick::Logger()->add('EventListener ' . $listener['class'] . ' must be instance of \Nick\Event\EventListenerInterface', Logger::TYPE_ERROR, 'Event');
        return FALSE;
      }
      try {
        if (!is_array($otherArgs)) {
          Nick::Logger()->add('The other arguments have to be of the array format.', Logger::TYPE_ERROR, 'Event');
          return FALSE;
        }

        if (!method_exists($class, $this->getEventName())) {
          Nick::Logger()->add(
            $this->translate(':eventName method does not exist in :extension listener class.',
              [
                ':eventName' => $this->getEventName(),
                ':extension' => $extension,
              ]),
            Logger::TYPE_ERROR,
            'Event');
          return FALSE;
        }
        // Call the listener class' method
        $class->{$this->getEventName()}($variables, ...$otherArgs);
      } catch (Exception $exception) {
        Nick::Logger()->add($exception->getMessage(), Logger::TYPE_ERROR, 'Event');
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
  protected function getListeners(): array {
    $extensions = Nick::ExtensionManager()::getInstalledExtensions();
    $listeners = [];
    foreach ($extensions as $extension) {
      // Skip if this extension has no info file.
      if (!$extInfo = Nick::ExtensionManager()::getExtensionInfo($extension['name'])) {
        continue;
      }

      // Skip if this extension does not listen to this (or any) event
      if (!isset($extInfo['event_listeners'][$this->getEventName()])) {
        continue;
      }

      // Extension listens to this event, add to array
      $listeners[$extension['name']] = $extInfo['event_listeners'][$this->getEventName()];
    }

    return $listeners;
  }

  /**
   * {@inheritDoc}
   */
  public function getEventName(): string {
    return $this->eventName;
  }

  /**
   * Sets the name of the event to be fired.
   *
   * @param string $eventName
   *
   * @return self
   */
  protected function setEventName(string $eventName): self {
    $this->eventName = $eventName;
    return $this;
  }

}