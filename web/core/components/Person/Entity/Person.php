<?php

namespace Nick\Person\Entity;

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
    $this->setType('person');
    $this->setValues($values);
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
    return \Nick::Session()->get('NPersonID', 0);
  }

  /**
   * @return array
   */
  public static function initialFields(): array {
    $language_options = \Nick::LanguageManager()->getAvailableLanguages();
    foreach ($language_options as $langcode => $language) {
      $language_options[$langcode] = '[' . $langcode . '] ' . $language['language'] . ' - ' . $language['country'];
    }

    $permissions = [];
    foreach (\Nick::PermissionManager()->getAllPermissions() as $permission) {
      $permissions[$permission->getPermission()] = $permission->getPermission();
    }
    return [
      'name' => [
        'type' => 'varchar',
        'length' => 255,
        'unique' => TRUE,
        'form' => [
          'title' => 'Name',
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
          'title' => 'Password',
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
          'title' => 'Language',
          'type' => 'select',
          'options' => $language_options,
        ],
      ],
      'permissions' => [
        'type' => 'blob',
        'form' => [
          'title' => 'Permissions',
          'type' => 'select',
          'options' => $permissions,
          'attributes' => [
            'multiple' => TRUE,
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
   * @return string
   */
  public function getTitle(): string {
    return $this->getName();
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