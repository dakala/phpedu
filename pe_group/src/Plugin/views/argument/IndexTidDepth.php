<?php

/**
 * @file
 * Definition of Drupal\taxonomy\Plugin\views\argument\IndexTidDepth.
 */

namespace Drupal\pe_group\Plugin\views\argument;

use Drupal\views\Plugin\views\argument\ArgumentPluginBase;
use Drupal\Component\Utility\String;
use Drupal\taxonomy\Entity\Term;

/**
 * Argument handler for taxonomy terms with depth.
 *
 * This handler is actually part of the node table and has some restrictions,
 * because it uses a subquery to find nodes with.
 *
 * @ingroup views_argument_handlers
 *
 * @ViewsArgument("taxonomy_user_tid")
 */
class IndexTidDepth extends ArgumentPluginBase {

  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['depth'] = array('default' => 0);
    $options['break_phrase'] = array('default' => FALSE, 'bool' => TRUE);
    $options['use_taxonomy_term_path'] = array('default' => FALSE, 'bool' => TRUE);
    $options['negate'] = array('default' => FALSE, 'bool' => TRUE);
    return $options;
  }

  public function buildOptionsForm(&$form, &$form_state) {
    $form['depth'] = array(
      '#type' => 'weight',
      '#title' => t('Depth'),
      '#default_value' => $this->options['depth'],
      '#description' => t('The depth will match nodes tagged with terms in the hierarchy. For example, if you have the term "fruit" and a child term "apple", with a depth of 1 (or higher) then filtering for the term "fruit" will get nodes that are tagged with "apple" as well as "fruit". If negative, the reverse is true; searching for "apple" will also pick up nodes tagged with "fruit" if depth is -1 (or lower).'),
    );

    $form['break_phrase'] = array(
      '#type' => 'checkbox',
      '#title' => t('Allow multiple values'),
      '#description' => t('If selected, users can enter multiple values in the form of 1+2+3. Due to the number of JOINs it would require, AND will be treated as OR with this filter.'),
      '#default_value' => !empty($this->options['break_phrase']),
    );

    $form['negate'] = array(
      '#type' => 'checkbox',
      '#title' => t('Negate'),
      '#description' => t('If selected, the filter criteria will be negated i.e. users who are NOT tagged with the selected taxonomy term(s) will be matched.'),
      '#default_value' => !empty($this->options['negate']),
    );

    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * Override defaultActions() to remove summary actions.
   */
  protected function defaultActions($which = NULL) {
    if ($which) {
      if (in_array($which, array('ignore', 'not found', 'empty', 'default'))) {
        return parent::defaultActions($which);
      }
      return;
    }
    $actions = parent::defaultActions();
    unset($actions['summary asc']);
    unset($actions['summary desc']);
    unset($actions['summary asc by count']);
    unset($actions['summary desc by count']);
    return $actions;
  }

  public function query($group_by = FALSE) {
    $this->ensureMyTable();

    if (!empty($this->options['break_phrase'])) {
      $tids = new \stdClass();
      $tids->value = $this->argument;
      $tids = $this->breakPhrase($this->argument, $tids);
      if ($tids->value == array(-1)) {
        return FALSE;
      }
      $tids = $tids->value;
    }
    else {
      $tids = (array) $this->argument;
    }

    $operator = (!empty($this->options['negate'])) ? 'NOT IN' : 'IN';

    if ($this->options['depth'] > 0) {
      foreach ($tids as $tid) {
        $ids = \Drupal::service('group.manager')
          ->getTaxonomyTermChildren($tid, $this->options['depth']);
        if (!empty($ids)) {
          $tids = array_merge((array) $tid, $ids);
        }
      }
    }
    elseif ($this->options['depth'] < 0) {
      $depth = strval($this->options['depth']);
      foreach ($tids as $tid) {
        $ids = \Drupal::service('group.manager')
          ->getTaxonomyTermParents($tid, intval(substr($depth, 1)));
        if (!empty($ids)) {
          $tids = array_merge((array) $tid, $ids);
        }
      }
    }

    $uids = array();
    $terms = entity_load_multiple('taxonomy_term', $tids);
    foreach ($terms as $term) {
      if ($term instanceof Term) {
        $ids = \Drupal::service('group.manager')
          ->getTermEntityIds($term, 'user');
        if (!empty($ids)) {
          $uids = array_merge($uids, $ids);
        }
      }
    }

    // no users tagged with any of the selected terms.
    if (empty($uids)) {
      $uids = array('-1');
    }
    // add the filter to the query.
    $this->query->addWhere(0, "$this->tableAlias.uid", $uids, $operator);
  }


  function title() {
    $term = entity_load('taxonomy_term', $this->argument);
    if (!empty($term)) {
      return String::checkPlain($term->getName());
    }
    // TODO review text
    return t('No name');
  }

}
