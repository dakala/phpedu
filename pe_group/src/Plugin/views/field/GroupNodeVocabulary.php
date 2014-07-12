<?php

/**
 * @file
 * Definition of Drupal\pe_group\Plugin\views\field\GroupNodeVocabulary.
 */

namespace Drupal\pe_group\Plugin\views\field;

use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\field\Numeric;

/**
 * Field handler to present a link to user edit.
 *
 * @ingroup views_field_handlers
 *
 * @PluginID("group_node_vid")
 */
class GroupNodeVocabulary extends Numeric {

  /**
   * {@inheritdoc}
   */
  public function usesGroupBy() {
    return FALSE;
  }

  public function query() {
    $this->addAdditionalFields();
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $value = array();
    if ($entity = $this->getEntity($values)) {
      $value = \Drupal::service('group.manager')
        ->getGroupEntityReferenceTids($entity);
    }
    return (count($value)) ? array_pop($value) : 0;
  }

}
