<?php

/**
 * @file
 * Contains \Drupal\pe_group\Form\UserRoleDelete.
 */

namespace Drupal\pe_group\Form;

use Drupal\Core\Entity\ContentEntityConfirmFormBase;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;


/**
 * Provides a deletion confirmation form for Role entity.
 */
class UserRoleDelete extends \Drupal\user\Form\UserRoleDelete {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete the group role %name?', array('%name' => $this->entity->label()));
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelRoute() {
    return array(
      'route_name' => 'group.role_list',
    );
  }

  /**
   * {@inheritdoc}
   */
  public function submit(array $form, FormStateInterface $form_state) {
    $this->entity->delete();
    watchdog('group', 'Group Role %name has been deleted.', array('%name' => $this->entity->label()));
    drupal_set_message($this->t('Group Role %name has been deleted.', array('%name' => $this->entity->label())));
    $form_state->setRedirect('group.role_list');
  }

}
