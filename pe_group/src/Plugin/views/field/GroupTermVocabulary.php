<?php

/**
 * @file
 * Definition of Drupal\pe_group\Plugin\views\field\GroupNodeVocabulary.
 */

namespace Drupal\pe_group\Plugin\views\field;

use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\field\FieldPluginBase;

/**
 * Field handler to present a link to user edit.
 *
 * @ingroup views_field_handlers
 *
 * @PluginID("group_term_vid")
 */
class GroupTermVocabulary extends FieldPluginBase {

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
    return $this->getEntity($values)->vid->value;
  }

}
