<?php

namespace Nick;

use Nick;
use Nick\Person\Person;

/**
 * Class Logger
 *
 * @package Nick
 */
class Logger {

  /** Logging type constants */
  const TYPE_WARNING = 1;
  const TYPE_ERROR = 2;
  const TYPE_INFO = 3;
  const TYPE_SUCCESS = 4;
  const TYPE_FAILURE = 5;

  /** @var Database $database */
  protected $database;

  /** @var Renderer $renderer */
  protected $renderer;

  /**
   * Logger constructor.
   */
  public function __construct() {
    $this->database = \Nick::Database();
  }

  /**
   * @param $message
   *    The message the log should include
   * @param int $type
   *    The type of this log (constants in Logger class)
   * @param string $category
   *    The category this log belongs to
   *
   * @return bool
   */
  public function add($message, $type = self::TYPE_INFO, $category = 'php') {
    $data = [
      'id' => 0,
      'type' => $type,
      'page' => $_SERVER['REQUEST_URI'],
      'owner' => Person::getCurrentUser(),
      'backtrace' => serialize(debug_backtrace()),
      'category' => $category,
      'message' => $message,
      'rendered' => 0,
    ];

    $query = $this->database->insert('logs');
    $query->values($data);
    if (!$result = $query->execute()) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * @return array
   */
  protected function typesToStrings() {
    return [
      self::TYPE_WARNING => 'warning',
      self::TYPE_ERROR => 'error',
      self::TYPE_INFO => 'info',
      self::TYPE_SUCCESS => 'success',
      self::TYPE_FAILURE => 'failure',
    ];
  }

  /**
   * @return array|bool
   */
  protected function getUnrenderedLogs() {
    $logs = $this->database->select('logs')
      ->condition('rendered', 0);

    if (!$logs->execute()) {
      return FALSE;
    }

    return $logs->fetchAllAssoc();
  }

  /**
   * @param $id
   *
   * @return bool
   */
  protected function setRendered($id) {
    $query = $this->database->update('logs')
      ->condition('id', $id)
      ->values(['rendered' => 1]);
    return $query->execute();
  }

  /**
   * @return NULL|string
   */
  public function results() {
    if (!$results = $this->getUnrenderedLogs()) {
      return NULL;
    }

    foreach ($results as $result) {
      $this->setRendered($result['id']);
    }

    return $results;
  }

}