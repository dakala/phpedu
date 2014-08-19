<?php

/**
 * @file
 * Contains \Drupal\user\Plugin\Action\ChangeUserRoleBase.
 */

namespace Drupal\pe_group\Plugin\Action;

use Drupal\Core\Action\ConfigurableActionBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a base class for operations to change a user's role.
 */
abstract class ChangeUserRoleBase extends ConfigurableActionBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array(
      'rid' => '',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $roles = group_role_names(TRUE);
    unset($roles[GROUP_MEMBER_RID]);
    $form['rid'] = array(
      '#type' => 'radios',
      '#title' => t('Group Role'),
      '#options' => $roles,
      '#default_value' => $this->configuration['rid'],
      '#required' => TRUE,
    );
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['rid'] = $form_state->getValue('rid');
  }

}
