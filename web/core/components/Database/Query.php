<?php

namespace Nick\Database;

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
   * @return self
   */
  public function execute($query) {
    if (isset($this->settings['debugging']) && $this->settings['debugging']) {
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