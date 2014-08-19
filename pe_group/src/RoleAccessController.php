<?php

/**
 * @file
 * Contains \Drupal\user\RoleAccessController.
 */

namespace Drupal\pe_group;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access controller for the user_role entity type.
 */
class RoleAccessController extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, $langcode, AccountInterface $account) {
    switch ($operation) {
      case 'delete':
        if (in_array($entity->id(), array(GROUP_MEMBER_RID, GROUP_ADMINISTRATOR_RID, GROUP_SECRETARY_RID, GROUP_LEADER_RID))) {
          return FALSE;
        }

      default:
        return parent::checkAccess($entity, $operation, $langcode, $account);
    }
  }

}
