<?php

/**
 * @file
 * Definition of Drupal\user\Entity\User.
 */

namespace Drupal\pe_group\Entity;

use Drupal\Core\Entity\ContentEntityBase;
//use Drupal\Core\Entity\EntityStorageControllerInterface;
use Drupal\Core\Entity\EntityMalformedException;
use Drupal\Core\Field\FieldDefinition;
use Drupal\user\Entity\User;
//use Drupal\user\UserInterface;
use Drupal\pe_group\GroupUserInterface;

class GroupUser extends User {

  /**
   * {@inheritdoc}
   */
  public function getGroupRoles() {
    // @todo:
    $roles = array();
    foreach ($this->get('group_roles') as $role) {
      $roles[] = $role->value;
    }
    return $roles;
  }

  /**
   * {@inheritdoc}
   */
  public function hasGroupRole($rid) {
    return in_array($rid, $this->getGroupRoles());
  }

  /**
   * {@inheritdoc}
   */
  public function addGroupRole($rid) {
    $roles = $this->getGroupRoles();
    $roles[] = $rid;
    $this->set('roles', array_unique($roles));
  }

  /**
   * {@inheritdoc}
   */
  public function removeGroupRole($rid) {
    $this->set('roles', array_diff($this->getGroupRoles(), array($rid)));
  }

  /**
   * {@inheritdoc}
   */
  public function hasPermission($permission) {
    // User #1 has all privileges.
    if ((int) $this->id() === 1) {
      return TRUE;
    }

    $roles = \Drupal::entityManager()->getStorage('group_role')->loadMultiple($this->getGroupRoles());

    foreach ($roles as $role) {
      if ($role->hasPermission($permission)) {
        return TRUE;
      }
    }

    return FALSE;
  }
}
