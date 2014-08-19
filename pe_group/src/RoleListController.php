<?php

/**
 * @file
 * Contains \Drupal\user\RoleListController.
 */

namespace Drupal\pe_group;

use Drupal\Core\Config\Entity\DraggableListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a listing of user roles.
 */
class RoleListController extends DraggableListBuilder  {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'user_admin_group_roles_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = t('Name');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $this->getLabel($entity);
    return $row + parent::buildRow($entity);
  }

  /**
   * {@inheritdoc}
   */
  public function getOperations(EntityInterface $entity) {
    $operations = parent::getOperations($entity);

    if ($entity->hasLinkTemplate('edit-group-permissions-form')) {
      $operations['permissions'] = array(
        'title' => t('Edit permissions'),
        'weight' => 20,
      ) + $entity->urlInfo('edit-group-permissions-form');
    }
    return $operations;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    drupal_set_message(t('The group role settings have been updated.'));
  }

}
