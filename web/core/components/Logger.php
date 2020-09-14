<?php

namespace Nick;

use Nick;
use Nick\Database\Database;
use Nick\Person\Person;
use Nick\Translation\StringTranslation;

/**
 * Class Logger
 *
 * @package Nick
 */
class Logger {

  use StringTranslation;

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
    $this->database = Nick::Database();
    $this->renderer = Nick::Renderer();
  }

  /**
   * @param        $message
   *    The message the log should include
   * @param int    $type
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
      'owner' => Person::getCurrentPerson(),
      'backtrace' => serialize(debug_backtrace()),
      'category' => $category,
      'message' => $this->translate(':message', [':message' => $message]),
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

    if (!$logs_result = $logs->execute()) {
      return FALSE;
    }

    return $logs_result->fetchAllAssoc();
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
  public function render() {
    $types = self::typesToStrings();

    if (!$results = $this->getUnrenderedLogs()) {
      return NULL;
    }
    $renders = [];
    foreach ($results as $result) {
      if (!$render = $this->renderer->setType('logger')->setTemplate($types[$result['type']])) {
        self::add('[Logger][render]: Something went wrong trying to find the log render template file.');
        continue;
      }
      $variables = [
        'message' => $result['message'],
      ];
      $renders[] = $render->render($variables);
      $this->setRendered($result['id']);
    }

    $render = '';
    foreach ($renders as $item) {
      $render .= $item;
    }
    return $render ?? NULL;
  }

}