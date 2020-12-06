<?php

namespace Nick\Permission;

use Nick\Database\Result;

/**
 * Class PermissionManager
 *
 * @package Nick\Permissions
 */
class PermissionManager {

  /**
   * Returns Permission object
   *
   * @param $permission
   *
   * @return Permission
   */
  public function getPermission($permission): Permission {
    $object = new Permission();
    return $object->load($permission);
  }

  /**
   * Returns all permissions
   *
   * @return bool|array
   */
  public function getAllPermissions() {
    $storage = \Nick::Database()
      ->select('permissions')
      ->fields(['permission'])
      ->execute();
    if (!$storage instanceof Result) {
      return FALSE;
    }

    $permissions = [];
    $results = $storage->fetchAllAssoc();
    foreach ($results as $result) {
      $permissions[] = $this->getPermission($result['permission']);
    }

    return $permissions;
  }

}
