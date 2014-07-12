<?php

/**
 * @file
 * Contains Drupal\pe_group\Entity\Role.
 */

namespace Drupal\pe_group\Entity;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\pe_group\RoleInterface;

/**
 * Defines the group role entity class.
 *
 * @ConfigEntityType(
 *   id = "group_role",
 *   label = @Translation("Group Role"),
 *   controllers = {
 *     "storage" = "Drupal\pe_group\GroupRoleStorage",
 *     "access" = "Drupal\pe_group\RoleAccessController",
 *     "list_builder" = "Drupal\pe_group\GroupRoleListBuilder",
 *     "form" = {
 *       "default" = "Drupal\pe_group\RoleFormController",
 *       "delete" = "Drupal\pe_group\Form\UserRoleDelete"
 *     }
 *   },
 *   admin_permission = "administer permissions",
 *   config_prefix = "role",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "weight" = "weight",
 *     "label" = "label"
 *   },
 *   links = {
 *     "delete-form" = "group.role_delete",
 *     "edit-form" = "group.role_edit",
 *
 *   }
 * )
 */

// "edit-group-permissions-form" = "user.admin_permission"
class Role extends ConfigEntityBase implements RoleInterface {

  /**
   * The machine name of this role.
   *
   * @var string
   */
  public $id;

  /**
   * The UUID of this role.
   *
   * @var string
   */
  public $uuid;

  /**
   * The human-readable label of this role.
   *
   * @var string
   */
  public $label;

  /**
   * The weight of this role in administrative listings.
   *
   * @var int
   */
  public $weight;

  /**
   * The permissions belonging to this role.
   *
   * @var array
   */
  public $permissions = array();

  /**
   * {@inheritdoc}
   */
  public function getPermissions() {
    return $this->permissions;
  }

  /**
   * {@inheritdoc}
   */
  public function hasPermission($permission) {
    return in_array($permission, $this->permissions);
  }

  /**
   * {@inheritdoc}
   */
  public function grantPermission($permission) {
    if (!$this->hasPermission($permission)) {
      $this->permissions[] = $permission;
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function revokePermission($permission) {
    $this->permissions = array_diff($this->permissions, array($permission));
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function postLoad(EntityStorageInterface $storage, array &$entities) {
    parent::postLoad($storage, $entities);
    // Sort the queried roles by their weight.
    // See \Drupal\Core\Config\Entity\ConfigEntityBase::sort().
    uasort($entities, 'static::sort');
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    if (!isset($this->weight) && ($roles = $storage->loadMultiple())) {
      // Set a role weight to make this new role last.
      $max = array_reduce($roles, function($max, $role) {
        return $max > $role->weight ? $max : $role->weight;
      });
      $this->weight = $max + 1;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    Cache::invalidateTags(array('group_role' => $this->id()));
    // Clear render cache.
    entity_render_cache_clear();
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    parent::postDelete($storage, $entities);

    $ids = array_keys($entities);
    $storage->deleteRoleReferences($ids);
    Cache::invalidateTags(array('group_role' => $ids));
  }

}
