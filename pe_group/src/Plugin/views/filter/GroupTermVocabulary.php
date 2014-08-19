<?php

/**
 * @file
 * Definition of Drupal\pe_group\Plugin\views\field\GroupNodeVocabulary.
 */

namespace Drupal\pe_group\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Field handler to present a link to user edit.
 *
 * @ingroup views_field_handlers
 *
 * @PluginID("group_term_vid")
 */
class GroupTermVocabulary extends FilterPluginBase {

  public function adminSummary() {}

  protected function operatorForm(&$form, FormStateInterface $form_state) {}

  public function canExpose() {
    return FALSE;
  }

  public function query() {
    $groups = $this->getGroupVocabularies();
    if (!count($groups)) {
      return;
    }
    $this->ensureMyTable();
    $this->query->addWhere($this->options['group'], 'taxonomy_term_data.vid', $groups);
  }

  public function getGroupVocabularies() {
    return \Drupal::service('group.manager')->getAllGroupVocabularies();
  }
}
