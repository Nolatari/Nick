<?php

namespace Nick\Person;

use Nick\Matter\MatterInterface;

/**
 * Interface PersonInterface
 *
 * @package Nick\Person
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

}