<?php

namespace Nick\Pages;

use Nick\Matter\Matter;
use Nick\Matter\MatterInterface;

/**
 * Class Pages
 *
 * @package Nick\Pages
 */
class Pages Extends Matter implements PagesInterface {

  /**
   * Pages constructor.
   *
   * @param null|array $values
   */
  public function __construct($values = NULL) {
    parent::__construct($values);
    $this->setType('pages');
  }

  /**
   * @param int $id
   *
   * @return MatterInterface|NULL
   */
  public static function load(int $id) {
    return parent::loadMatter($id, 'page');
  }

  /**
   * @return array
   */
  public static function loadMultiple() {
    return parent::loadMultipleMatters('page');
  }

  /**
   * @return string|null
   */
  public static function create() {
    return parent::createMatter('page');
  }

  /**
   * @return array
   */
  public static function initialFields(): array {
    return [
      'pid' => [
        'type' => 'varchar',
        'length' => 255,
        'form' => [
          'type' => 'textbox',
          'name' => 'Page ID',
          'required' => TRUE,
        ],
      ],
      'controller' => [
        'type' => 'varchar',
        'length' => 255,
        'form' => [
          'type' => 'textbox',
          'name' => 'Controller',
          'required' => TRUE,
        ],
      ],
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getPid(): string {
    return $this->getValue('pid');
  }

  /**
   * {@inheritDoc}
   */
  public function setPid(string $pid) {
    return $this->setValue('pid', $pid);
  }

  /**
   * {@inheritDoc}
   */
  public function getController(): string {
    return $this->getValue('controller');
  }

  /**
   * {@inheritDoc}
   */
  public function setController(string $controller) {
    return $this->setValue('controller', $controller);
  }

}
