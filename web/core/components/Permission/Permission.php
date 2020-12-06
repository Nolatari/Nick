<?php

namespace Nick\Permission;

use Nick\Database\Result;

/**
 * Class Permission
 *
 * @package Nick\Permission
 */
class Permission {

  /** @var string $permission */
  protected string $permission;

  /** @var array $usage */
  protected array $usage;

  /**
   * Permissions constructor.
   *
   * @param string|null $permission
   */
  public function __construct(?string $permission = NULL) {
    if (!is_null($permission)) {
      $this->setPermission($permission);
    }
  }

  /**
   * @param string $permission
   *
   * @return bool|self
   */
  public function load(string $permission) {
    $storage = \Nick::Database()
      ->select('permissions')
      ->condition('permission', $permission)
      ->execute();
    if (!$storage instanceof Result) {
      return FALSE;
    }

    $result = $storage->fetchAllAssoc();
    $result = reset($result);
    if (!$result) {
      return FALSE;
    }
    return $this->setPermission($result['permission'])->setUsage(unserialize($result['usage']));
  }

  /**
   * Adds or saves permission
   *
   * @return bool
   */
  public function save(): bool {
    if (!$this->load($this->getPermission())) {
      $query = \Nick::Database()
        ->insert('permissions')
        ->values([
          'permission' => $this->getPermission(),
          'usage' => serialize($this->getUsage()),
        ])
        ->execute();
    } else {
      $query = \Nick::Database()
        ->update('permissions')
        ->values([
          'usage' => serialize($this->getUsage()),
        ])
        ->condition('permission', $this->getPermission())
        ->execute();
    }
    if (!$query instanceof Result) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * Returns permission
   *
   * @return string
   */
  public function getPermission(): string {
    return $this->permission;
  }

  /**
   * Sets permission for object
   *
   * @param string $permission
   *
   * @return Permission
   */
  public function setPermission(string $permission): self {
    $this->permission = $permission;
    return $this;
  }

  /**
   * Returns usage
   *
   * @return array
   */
  public function getUsage(): array {
    return $this->usage;
  }

  /**
   * Adds usage to permission
   *
   * @param $usage
   *
   * @return Permission
   */
  public function addUsage($usage): self {
    $this->usage[] = $usage;
    return $this;
  }

  /**
   * Sets usage array to given array
   *
   * @param array $usage
   *
   * @return $this
   */
  public function setUsage(array $usage): self {
    $this->usage = $usage;
    return $this;
  }

}
