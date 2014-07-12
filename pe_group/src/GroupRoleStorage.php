<?php

/**
 * @file
 * Contains \Drupal\user\RoleStorageController.
 */

namespace Drupal\pe_group;

use Drupal\Core\Config\Entity\ConfigEntityStorage;

/**
 * Controller class for user roles.
 */
class GroupRoleStorage extends ConfigEntityStorage implements GroupRoleStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function deleteRoleReferences(array $rids) {
    // Remove the group role from all users.
    db_delete('users_group_roles')
      ->condition('rid', $rids)
      ->execute();
  }

  public function loadGroupRoles(\Drupal\taxonomy\Entity\Term $term, array $users) {
    $query = db_select('users_group_roles', 'ugr');
    $query->join('taxonomy_term_machine_name', 't', 'ugr.tid = t.tid');
    $query->fields('ugr');
    $query->fields('t', array('machine_name'));
    $query->condition('ugr.tid', $term->id());
    if (count($users)) {
      $query->condition('ugr.uid', array_keys($users));
    }
    return $query->execute()->fetchAll();
  }

  public function getGroupRolesForGroup(\Drupal\taxonomy\Entity\Term $term, array $users) {
    $group_roles = array();
    $roles = $this->loadGroupRoles($term, $users);
    foreach ($roles as $role) {
      $group_roles[$role->machine_name][$role->uid][] = $role->rid;
    }
    return $group_roles;
  }

  /**
   * Add the group role to this user.
   *
   * @param $account
   * @param $rid
   * @param $tid
   */
  public function addGroupRole($account, $rid, $tid) {
    db_insert('users_group_roles')
      ->fields(array(
        'uid' => $account->id(),
        'tid' => $tid,
        'rid' => $rid,
      ))->execute();
  }

  /**
   * Remove the group role from this user.
   *
   * @param $account
   * @param $rid
   * @param $tid
   */
  public function removeGroupRole($account, $rid, $tid) {
    db_delete('users_group_roles')
      ->condition('uid', $account->id())
      ->condition('tid', $tid)
      ->condition('rid', $rid)
      ->execute();
  }

}
