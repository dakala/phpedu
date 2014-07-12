<?php

/**
 * @file
 * Contains \Drupal\user\Plugin\Action\RemoveRoleUser.
 */

namespace Drupal\pe_group\Plugin\Action;

use Drupal\pe_group\Plugin\Action\ChangeUserGroupBase;

/**
 * Removes a user from a group.
 *
 * @Action(
 *   id = "user_remove_group_action",
 *   label = @Translation("Remove the selected users from a group"),
 *   type = "user"
 * )
 */
class RemoveGroupUser extends ChangeUserGroupBase {

  /**
   * {@inheritdoc}
   */
  public function execute($account = NULL) {
    $tid = $this->configuration['tid'];
    $field = \Drupal::service('group.manager')->loadTermReferenceField(entity_load('taxonomy_term', $tid), 'user');
    unset($account->{$field->name}->target_id);
    $account->save();
  }

}
