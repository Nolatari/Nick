<?php

namespace Nick\Matter;

use Exception;

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
   * @param string $type
   * @param array $values
   *
   * @return MatterInterface|bool
   */
  public function getStorage($type, $values = []);

  /**
   * @return bool|array
   */
  public function getAllFields();

  /**
   * @param array $properties
   *          An array of properties your Matter should have
   * @param bool $multiple
   *          If you expect multiple results, set this to TRUE
   *
   * @return bool|array
   *
   * @throws Exception
   */
  public function loadByProperties($properties = [], $multiple = FALSE);

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
   * Validates node before saving.
   *
   * @return bool
   */
  public function validate();

  /**
   * @return bool
   */
  public function save();

  /**
   * @return int|string|NULL
   */
  public function id();

}