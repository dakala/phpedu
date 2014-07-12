<?php

/**
 * @file
 * Contains \Drupal\pe_group\Plugin\views\field\GroupBulkForm.
 */

namespace Drupal\pe_group\Plugin\views\field;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\system\Plugin\views\field\BulkForm;
use Drupal\Core\Cache\Cache;

/**
 * Defines a group operations bulk form element.
 *
 * @PluginID("group_bulk_form")
 */
class GroupBulkForm extends BulkForm {

  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['action_title'] = array('default' => 'With ALL selections', 'translatable' => TRUE);

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, &$form_state) {
//    $form['action_title'] = array(
//      '#type' => 'textfield',
//      '#title' => t('Action title'),
//      '#default_value' => $this->options['action_title'],
//      '#description' => t('The title shown above the actions dropdown.'),
//    );

    debug(array('foo:'));
    parent::buildOptionsForm($form, $form_state);
  }
  /**
   * {@inheritdoc}
   */
  public function viewsFormSubmit(&$form, &$form_state) {
    parent::viewsFormSubmit($form, $form_state);

    if ($form_state['step'] == 'views_form_views_form') {
      Cache::invalidateTags(array('content' => TRUE));
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function emptySelectedMessage() {
    return t('No content selected.');
  }

}
