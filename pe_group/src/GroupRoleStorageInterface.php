<?php

/**
 * @file
 * Contains \Drupal\user\RoleStorageControllerInterface.
 */

namespace Drupal\pe_group;

use Drupal\Core\Config\Entity\ConfigEntityStorageInterface;

/**
 * Defines a common interface for roel entity controller classes.
 */
interface GroupRoleStorageInterface extends ConfigEntityStorageInterface {

  /**
   * Delete role references.
   *
   * @param array $rids
   *   The list of role IDs being deleted. The storage controller should
   *   remove permission and user references to this role.
   */
  public function deleteRoleReferences(array $rids);

  public function loadGroupRoles(\Drupal\taxonomy\Entity\Term $term, array $users);

  public function addGroupRole($account, $rid, $tid);

  public function removeGroupRole($account, $rid, $tid);

}
