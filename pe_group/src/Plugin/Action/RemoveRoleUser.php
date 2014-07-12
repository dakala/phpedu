<?php

/**
 * @file
 * Contains \Drupal\user\Plugin\Action\RemoveRoleUser.
 */

namespace Drupal\pe_group\Plugin\Action;

use Drupal\pe_group\Plugin\Action\ChangeUserRoleBase;

/**
 * Removes a role from a user.
 *
 * @Action(
 *   id = "user_remove_group_role_action",
 *   label = @Translation("Remove a group role from the selected users"),
 *   type = "user"
 * )
 */
class RemoveRoleUser extends ChangeUserRoleBase {

  /**
   * {@inheritdoc}
   */
  public function execute($account = NULL) {
    $tid = $this->configuration['tid'];
    $rid = $this->configuration['rid'];
    // Skip removing the role from the user if they already don't have it.
    if ($account !== FALSE && \Drupal::service('group.manager')
        ->hasGroupRole($account, $rid, $tid)) {
      \Drupal::entityManager()
        ->getStorage('group_role')
        ->removeGroupRole($account, $rid, $tid);
    }
  }

}
