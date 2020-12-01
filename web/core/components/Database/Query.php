<?php

namespace Nick\Database;

use Nick\Settings;

/**
 * Class Query
 *
 * @package Nick\Database
 */
class Query extends Database {

  /**
   * Query constructor.
   *
   * @param $query
   */
  public function __construct($query) {
    parent::__construct($this->condition_delimiter, $this->getDatabaseName());
    $this->execute($query);
  }

  /**
   * Executes the query.
   *
   * @param $query
   *
   * @return Query
   */
  protected function execute($query) {
    if (Settings::get('debugging')) {
      d($query);
    }
    if ($result = $this->database->query($query)) {
      $this->query = [
        'useQuery' => TRUE,
        'query' => $result,
      ];
      $this->result = $result;
    }

    return $this;
  }

}