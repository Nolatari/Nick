<?php

namespace Nick\Form;

/**
 * Interface FormStateInterface
 *
 * @package Nick\Form
 */
interface FormStateInterface {

  /**
   * Populates values array with database values.
   *
   * @param bool $return
   *
   * @return bool
   */
  public function populateValueArray($return = FALSE);

  /**
   * Gets value array
   *
   * @return array
   */
  public function getValues();

  /**
   * Sets value array
   *
   * @param $values
   *
   * @return self
   */
  public function setValues($values);

  /**
   * @param string $key
   *
   * @return false|mixed
   */
  public function get(string $key);

  /**
   * @param string $key
   * @param string $value
   *
   * @return self
   */
  public function set(string $key, string $value);

  /**
   * Saves current form state to DB
   */
  public function save();

}
