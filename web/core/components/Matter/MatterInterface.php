<?php

namespace Nick\Matter;

/**
 * Interface MatterInterface
 *
 * @package Nick\Matter
 */
interface MatterInterface {

  /**
   * @return array
   */
  public static function fields();

  /**
   * @return string|NULL
   */
  public function getType();

  /**
   * @param string $key
   *
   * @return string|array|NULL
   */
  public function getValue(string $key);

  /**
   * @param string $key
   * @param mixed $value
   *
   * @return self|NULL
   */
  public function setValue(string $key, $value);

  /**
   * @return bool
   */
  public function save();

  /**
   * @return int|string|NULL
   */
  public function id();

}