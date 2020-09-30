<?php

namespace Nick\Person;

use Nick\Entity\EntityInterface;

/**
 * Interface PersonInterface
 *
 * @package Nick\Person
 */
interface PersonInterface extends EntityInterface {

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