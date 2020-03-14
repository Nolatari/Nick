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
   * Saves current form state to DB
   */
  public function save();

}
