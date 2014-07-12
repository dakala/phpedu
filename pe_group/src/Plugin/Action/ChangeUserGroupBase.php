<?php

/**
 * @file
 * Contains \Drupal\user\Plugin\Action\ChangeUserRoleBase.
 */

namespace Drupal\pe_group\Plugin\Action;

use Drupal\Core\Action\ConfigurableActionBase;

/**
 * Provides a base class for operations to change a user's group membership.
 */
abstract class ChangeUserGroupBase extends ConfigurableActionBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'tid' => '',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, array &$form_state) {
    $form['tid'] = array(
      '#type' => 'radios',
      '#title' => t('Group'),
      '#options' => pe_group_get_groups(),
      '#default_value' => $this->configuration['tid'],
      '#required' => TRUE,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, array &$form_state) {
    $this->configuration['tid'] = $form_state['values']['tid'];
  }

}
