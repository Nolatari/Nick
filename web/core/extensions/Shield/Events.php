<?php

namespace Nick\Shield;

use Nick\ArrayManipulation;
use Nick\Event\EventListener;
use Nick\Logger;
use Nick\Settings;

/**
 * Class Events
 *
 * @package Nick\Shield
 */
class Events extends EventListener {

  /**
   * {@inheritDoc}
   */
  public function postInit(?array &$cache) {
    $valid_passwords = Settings::get('shield') ?: ['nick' => 'nick'];
    $valid_users = array_keys($valid_passwords);

    $user = $_SERVER['PHP_AUTH_USER'] ?? '';
    $pass = $_SERVER['PHP_AUTH_PW'] ?? '';

    $validated = ArrayManipulation::contains($valid_users, $user) && $pass == $valid_passwords[$user];

    if (!$validated) {
      header('WWW-Authenticate: Basic realm="Nick\'s restricted area."');
      header('HTTP/1.0 401 Unauthorized');
      \Nick::Logger()->add(translate('HTTP Authentication failed.'), Logger::TYPE_WARNING, 'Shield');
      die(translate('Not authorized'));
    }
  }

}
