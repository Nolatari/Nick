<?php

namespace Nick\Person;

use Nick\Matter\MatterInterface;

/**
 * Interface UserInterface
 *
 * @package Nick\User
 */
interface PersonInterface extends MatterInterface {

  /**
   * @return array|mixed|NULL|string
   */
  public function getName();

  /**
   * @param string $password
   *
   * @return bool
   */
  public function checkPassword($password);

  /**
   * @return array|mixed|NULL|string
   */
  public function getFingerprint();

}