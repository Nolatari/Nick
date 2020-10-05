<?php

namespace Nick\Pages;

use Exception;
use Nick\Entity\Entity;
use Nick\Entity\EntityInterface;

/**
 * Class Pages
 *
 * @package Nick\Pages
 */
class Page extends Entity implements PageInterface {

  /**
   * Page constructor.
   *
   * @param null|array $values
   */
  public function __construct($values = NULL) {
    parent::__construct($values);
    $this->setType('page');
  }

  /**
   * @param int $id
   *
   * @return EntityInterface|NULL
   *
   * @throws Exception
   */
  public static function load(int $id) {
    return parent::loadEntity($id, 'page');
  }

  /**
   * @return array
   */
  public static function loadMultiple() {
    return parent::loadMultipleEntities('page');
  }

  /**
   * @return string|null
   */
  public static function create() {
    return parent::createEntity('page');
  }

  /**
   * @return array
   */
  public static function initialFields(): array {
    return [
      'pid' => [
        'type' => 'varchar',
        'length' => 255,
        'unique' => TRUE,
        'form' => [
          'type' => 'textbox',
          'name' => 'Page machine name',
          'required' => TRUE,
        ],
      ],
      'controller' => [
        'type' => 'varchar',
        'length' => 255,
        'form' => [
          'type' => 'textbox',
          'name' => 'Page Controller',
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
