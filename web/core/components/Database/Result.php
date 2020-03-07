<?php

namespace Nick\Database;

use mysqli_result;

/**
 * Class Result
 *
 * @package Nick\Database
 */
class Result {

  /** @var bool|mysqli_result $result */
  protected $result;

  /**
   * Result constructor.
   *
   * @param mysqli_result|bool $result
   */
  public function __construct($result) {
    $this->result = $result;
  }

  /**
   * fetchAllAssoc method
   *
   * @param string|null $key
   *
   * @return array|bool
   */
  public function fetchAllAssoc($key = NULL) {
    d($this->result);
    if (!$this->result instanceof mysqli_result) {
      return FALSE;
    }

    $records = [];
    while ($record = mysqli_fetch_assoc($this->result)) {
      if ($key !== NULL) {
        $records[$record[$key]] = $record;
      } else {
        $records[] = $record;
      }
    }

    $this->result = '';
    return $records;
  }

}
