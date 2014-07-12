<?php

/**
 * @file
 * Definition of Drupal\pe_group\Plugin\views\field\LinkEdit.
 */

namespace Drupal\pe_group\Plugin\views\field;

use Drupal\views\ResultRow;
use Drupal\views\Plugin\views\field\Boolean;
//use Drupal\views\Plugin\views\display\DisplayPluginBase;
//use Drupal\views\ViewExecutable;
//use Drupal\Core\Entity\EntityInterface;
//use Drupal\node\Plugin\views\field\Node;
//use Drupal\pe_group\GroupManager;
//use Drupal\Core\Session\AccountInterface;

/**
 * Field handler to present a link to user edit.
 *
 * @ingroup views_field_handlers
 *
 * @PluginID("groupstatus")
 */
class GroupStatus extends Boolean {

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

    if ($entity = $this->getEntity($values)) {
      $value = count(\Drupal::service('group.manager')
        ->getGroupEntityReferenceTids($entity));
    }

    if (!empty($this->options['not'])) {
      $value = !$value;
    }

    if ($this->options['type'] == 'custom') {
      return $value ? filter_xss_admin($this->options['type_custom_true']) : filter_xss_admin($this->options['type_custom_false']);
    }
    elseif (isset($this->formats[$this->options['type']])) {
      return $value ? $this->formats[$this->options['type']][0] : $this->formats[$this->options['type']][1];
    }
    else {
      return $value ? $this->formats['yes-no'][0] : $this->formats['yes-no'][1];
    }
  }

}
