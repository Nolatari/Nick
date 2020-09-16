<?php

namespace Nick\Person;

use Nick\Matter\Matter;
use Nick\Matter\MatterInterface;

/**
 * Class Person
 *
 * @package Nick\Person
 */
class Person extends Matter implements PersonInterface {

  /**
   * Person constructor.
   *
   * @param null|array $values
   */
  public function __construct($values = NULL) {
    parent::__construct($values);
    $this->setType('person');
  }

  /**
   * Changes UID to the given ID
   *
   * @param int $id
   */
  public static function changeTo(int $id) {
    $_SESSION['uid'] = $id;
  }

  /**
   * Destroys a person's session
   */
  public static function logout() {
    session_destroy();
    $_SESSION['uid'] = NULL;
    unset($_SESSION['uid']);
  }

  /**
   * @return int
   */
  public static function getCurrentPerson() {
    return $_SESSION['uid'] ?? 0;
  }

  /**
   * @param int $id
   *
   * @return Matter|MatterInterface|NULL
   */
  public static function load(int $id) {
    return parent::loadMatter($id, 'person');
  }

  /**
   * @return array
   */
  public static function loadMultiple() {
    return parent::loadMultipleMatters('person');
  }

  /**
   * @return string|null
   */
  public static function create() {
    return parent::createMatter('person');
  }

  /**
   * @return array
   */
  public static function initialFields(): array {
    return [
      'name' => [
        'type' => 'varchar',
        'length' => 255,
        'unique' => TRUE,
        'form' => [
          'type' => 'textbox',
          'attributes' => [
            'type' => 'username',
          ],
        ],
      ],
      'password' => [
        'type' => 'varchar',
        'length' => '255',
        'form' => [
          'type' => 'textbox',
          'attributes' => [
            'type' => 'password',
          ],
        ],
      ],
    ];
  }

  /**
   * Overwrite getValues function to not allow returning of password.
   *
   * @return array|NULL
   */
  public function getValues(): ?array {
    $values = parent::getValues();
    unset($values['password']);
    return $values;
  }

  /**
   * {@inheritDoc}
   */
  public function getName(): string {
    return $this->getValue('name');
  }

  /**
   * {@inheritDoc}
   */
  public function checkPassword($password): bool {
    return password_verify($password, $this->getValue('password'));
  }

  /**
   * {@inheritDoc}
   */
  public function checkPerson($field, $value) {
    $this->checkPassword($value);
  }

}