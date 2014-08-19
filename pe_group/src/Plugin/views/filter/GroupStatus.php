<?php

/**
 * @file
 * Contains \Drupal\comment\Plugin\views\filter\StatisticsLastUpdated.
 */

namespace Drupal\pe_group\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\BooleanOperator;

/**
 * Filter handler for the newer of last comment / node updated.
 *
 * @ingroup views_filter_handlers
 *
 * @PluginID("groupstatus")
 */
class GroupStatus extends BooleanOperator {

  public function adminSummary() { }

  protected function operatorForm(&$form, FormStateInterface $form_state) { }

  public function canExpose() { return FALSE; }

  public function query() {
//    $table = $this->ensureMyTable();
    $this->query->addWhereExpression($this->options['group'], "taxonomy_term_data_node.tid > 0");
  }

}
