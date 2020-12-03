<?php

namespace Nick\Rest\Entity;

use Nick\Entity\Entity;
use Symfony\Component\Uid\Uuid;

/**
 * Class Client
 * @package Nick\Rest\Entity
 */
class Client extends Entity implements ClientInterface {

  public static array $permissions = [
    'retrieve' => 'Retrieve',
    'transmit' => 'Transmit',
    'all' => 'All',
  ];

  /**
   * Person constructor.
   *
   * @param null|array $values
   */
  public function __construct($values = NULL) {
    $this->setValues($values);
    $this->setType('client');
  }

  /**
   * @return array
   */
  public static function initialFields(): array {
    return [
      'title' => [
        'type' => 'varchar',
        'length' => 255,
        'unique' => TRUE,
        'form' => [
          'title' => 'Title',
          'type' => 'textbox',
        ],
      ],
      'uuid' => [
        'type' => 'varchar',
        'length' => '255',
        'form' => [
          'title' => 'UUID',
          'type' => 'uuid',
          'default_value' => Uuid::v4(),
        ],
      ],
      'permissions' => [
        'type' => 'varchar',
        'length' => '100',
        'form' => [
          'title' => 'Permissions',
          'type' => 'select',
          'options' => static::$permissions,
        ],
      ],
    ];
  }

  /**
   * @return string
   */
  public function getTitle(): string {
    return $this->getValue('title');
  }

  /**
   * @param string $title
   *
   * @return self
   */
  public function setTitle(string $title): self {
    return $this->setValue('title', $title);
  }

  /**
   * @return array|mixed|string|NULL
   */
  public function getUuid() {
    return $this->getValue('uuid');
  }

  /**
   * @param string $uuid
   *
   * @return array|mixed|string|NULL
   */
  public function setUuid(string $uuid) {
    return $this->setValue('uuid', $uuid);
  }

  /**
   * @return array
   */
  public function getPermissions(): array {
    return unserialize($this->getValue('permissions'));
  }

  /**
   * @param array $permissions
   *
   * @return self
   */
  public function setPermissions(array $permissions): self {
    return $this->setValue('permissions', serialize($permissions));
  }

  /**
   * Checks if client has permission
   *
   * @param $permission
   *
   * @return bool
   */
  public function hasPermission($permission) {
    $permissions = $this->getPermissions();

    foreach ($permissions as $item) {
      if ($item === $permission || $item === 'all') {
        return TRUE;
      }
    }
    return FALSE;
  }

}
