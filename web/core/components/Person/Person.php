<?php

namespace Nick\Person;

use Nick\Entity\Entity;
use Nick\Entity\EntityInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class Person
 *
 * @package Nick\Person
 */
class Person extends Entity implements PersonInterface {

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
    \Nick::Session()->set('NPersonID', $id);
  }

  /**
   * Destroys a person's session
   */
  public static function logout() {
    session_destroy();
    \Nick::Session()->set('NPersonID', 0);
    \Nick::Session()->remove('NPersonID');
    unset($_SESSION['NPersonID']);
  }

  /**
   * @return int
   */
  public static function getCurrentPerson() {
    return \Nick::Session()->get('NPersonID', 0);;
  }

  /**
   * @param int $id
   *
   * @return Entity|EntityInterface|NULL
   */
  public static function load(int $id) {
    return parent::loadEntity($id, 'person');
  }

  /**
   * @return array
   */
  public static function loadMultiple() {
    return parent::loadMultipleEntities('person');
  }

  /**
   * @return string|null
   */
  public static function create() {
    return parent::createEntity('person');
  }

  /**
   * @return array
   */
  public static function initialFields(): array {
    $language_options = \Nick::LanguageManager()->getAvailableLanguages();
    foreach ($language_options as $langcode => $language) {
      $language_options[$langcode] = '[' . $langcode . '] ' . $language['language'] . ' - ' . $language['country'];
    }
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
      'language' => [
        'type' => 'varchar',
        'length' => '10',
        'form' => [
          'type' => 'select',
          'options' => $language_options,
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
   * @param string $name
   *
   * @return self
   */
  public function setName(string $name): self {
    return $this->setValue('name', $name);
  }

  /**
   * @return string
   */
  public function getLanguage(): string {
    return $this->getValue('language');
  }

  /**
   * @param string $language
   *
   * @return self
   */
  public function setLanguage(string $language): self {
    return $this->setValue('language', $language);
  }

  /**
   * {@inheritDoc}
   */
  public function checkPassword($password): bool {
    return password_verify($password, $this->getValue('password'));
  }

}