<?php

/**
 * @file
 * Definition of Drupal\pe_group\Plugin\views\field\GroupPostCount.
 */

namespace Drupal\pe_group\Plugin\views\field;

use Drupal\views\Plugin\views\field\Standard;
use Drupal\views\ResultRow;

/**
 * Field handler to present a link to user edit.
 *
 * @ingroup views_field_handlers
 *
 * @PluginID("postcount")
 */
class GroupPostCount extends Standard {

  public function query() {
    $this->addAdditionalFields();
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    return \Drupal::service('group.manager')
      ->countTermEntities($this->getEntity($values), 'node');
  }

}
