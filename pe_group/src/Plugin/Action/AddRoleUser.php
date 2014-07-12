<?php

/**
 * @file
 * Contains \Drupal\user\Plugin\Action\AddRoleUser.
 */

namespace Drupal\pe_group\Plugin\Action;

use Drupal\pe_group\Plugin\Action\ChangeUserRoleBase;
use Drupal;

/**
 * Adds a group role to a user.
 *
 * @Action(
 *   id = "user_add_group_role_action",
 *   label = @Translation("Add a group role to the selected users"),
 *   type = "user"
 * )
 */
class AddRoleUser extends ChangeUserRoleBase {

  /**
   * {@inheritdoc}
   */
  public function execute($account = NULL) {
    $tid = $this->configuration['tid'];
    $rid = $this->configuration['rid'];
    // Skip adding the group role to the user if they already have it.
    if ($account !== FALSE && (\Drupal::service('group.manager')
        ->hasGroupRole($account, $rid, $tid) === FALSE)) {
      \Drupal::entityManager()
        ->getStorage('group_role')
        ->addGroupRole($account, $rid, $tid);
    }
  }

}
