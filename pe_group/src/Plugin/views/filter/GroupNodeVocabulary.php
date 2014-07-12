<?php

/**
 * @file
 * Definition of Drupal\pe_group\Plugin\views\field\GroupNodeVocabulary.
 */

namespace Drupal\pe_group\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\FilterPluginBase;
use Drupal\views\Views;

/**
 * Field handler to present a link to user edit.
 *
 * @ingroup views_field_handlers
 *
 * @PluginID("group_node_vid")
 */
class GroupNodeVocabulary extends FilterPluginBase {

  public function adminSummary() {
  }

  protected function operatorForm(&$form, &$form_state) {
  }

  public function canExpose() {
    return FALSE;
  }

  public function query() {
    $groups = \Drupal::service('group.manager')->getAllGroupVocabularies();
    if (!count($groups)) {
      return;
    }

    $this->ensureMyTable();
    $this->query->addTable('taxonomy_index');
    $this->query->addTable('taxonomy_term_data', 'taxonomy_index');
    $this->query->addRelationship(
      'taxonomy_term_data',
      Views::pluginManager('join')->createInstance('standard', array(
        'table' => 'taxonomy_term_data',
        'field' => 'tid',
        'left_table' => 'taxonomy_index',
        'left_field' => 'tid',
      )),
      'taxonomy_index'
    );
    $this->query->addWhere($this->options['group'], 'taxonomy_term_data.vid', $groups);
  }

}
