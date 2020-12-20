<?php

namespace Nick\Shield;

use Nick\Event\EventListener;
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

    $user = $_SERVER['PHP_AUTH_USER'] ?? NULL;
    $pass = $_SERVER['PHP_AUTH_PW'] ?? NULL;

    $validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);

    if (!$validated) {
      header('WWW-Authenticate: Basic realm="Nick\'s restricted area."');
      header('HTTP/1.0 401 Unauthorized');
      die ("Not authorized");
    }
  }

}
