<?php

/**
 * @file
 * Contains \Drupal\user\RoleFormController.
 */

namespace Drupal\pe_group;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for the role entity edit forms.
 */
class RoleFormController extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $form['label'] = array(
      '#type' => 'textfield',
      '#title' => $this->t('Role name'),
      '#default_value' => $entity->label(),
      '#size' => 30,
      '#required' => TRUE,
      '#maxlength' => 64,
      '#description' => $this->t('The name for this role. Example: "Moderator", "Editorial board", "Site architect".'),
    );
    $form['id'] = array(
      '#type' => 'machine_name',
      '#default_value' => $entity->id(),
      '#required' => TRUE,
      '#disabled' => !$entity->isNew(),
      '#size' => 30,
      '#maxlength' => 64,
      '#machine_name' => array(
        'exists' => 'user_group_role_load',
      ),
    );
    $form['weight'] = array(
      '#type' => 'value',
      '#value' => $entity->get('weight'),
    );

    return parent::form($form, $form_state, $entity);
  }

  /**
   * {@inheritdoc}
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    $actions = parent::actions($form, $form_state);
    // Disable delete of new and built-in roles.
    $actions['delete']['#access'] = !$this->entity->isNew() && !in_array($this->entity->id(), array(GROUP_MEMBER_RID, GROUP_ADMINISTRATOR_RID, GROUP_SECRETARY_RID, GROUP_LEADER_RID));
    return $actions;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $entity = $this->entity;

    // Prevent leading and trailing spaces in role names.
    $entity->set('label', trim($entity->label()));
    $status = $entity->save();

    $uri = $entity->urlInfo();
    $edit_link = \Drupal::l($this->t('Edit'), $uri['route_name'], $uri['route_parameters'], $uri['options']);
    if ($status == SAVED_UPDATED) {
      drupal_set_message($this->t('Group role %label has been updated.', array('%label' => $entity->label())));
      watchdog('group', 'Group role %label has been updated.', array('%label' => $entity->label()), WATCHDOG_NOTICE, $edit_link);
    }
    else {
      drupal_set_message($this->t('Group role %label has been added.', array('%label' => $entity->label())));
      watchdog('group', 'Group role %label has been added.', array('%label' => $entity->label()), WATCHDOG_NOTICE, $edit_link);
    }

    $form_state->setRedirect('group.role_list');
  }

}
