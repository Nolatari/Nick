<?php

namespace Nick\Rest;

/**
 * Class Rest
 * @package Nick\Rest
 */
class Rest {

  /**
   * @param $information
   *
   * @return array
   */
  public static function Retrieve($information): array {
    if (!$information['entity']) {
      return ['message' => 'No entity was given'];
    }
  }

  /**
   * @param $information
   *
   * @return array
   */
  public static function Transmit($information): array {
    if (!$information['entity']) {
      return ['message' => 'No entity was given'];
    }
  }



}