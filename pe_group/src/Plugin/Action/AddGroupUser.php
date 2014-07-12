<?php

/**
 * @file
 * Contains \Drupal\user\Plugin\Action\AddRoleUser.
 */

namespace Drupal\pe_group\Plugin\Action;

use Drupal\pe_group\Plugin\Action\ChangeUserGroupBase;

/**
 * Adds a user to a group.
 *
 * @Action(
 *   id = "user_add_group_action",
 *   label = @Translation("Add selected users to a group"),
 *   type = "user"
 * )
 */
class AddGroupUser extends ChangeUserGroupBase {

  /**
   * {@inheritdoc}
   */
  public function execute($account = NULL) {
    $tid = $this->configuration['tid'];
    $field = \Drupal::service('group.manager')
      ->loadTermReferenceField(entity_load('taxonomy_term', $tid), 'user');
    if ($field) {
      $account->{$field->name}->target_id = $tid;
      $account->save();
    }
  }

}
