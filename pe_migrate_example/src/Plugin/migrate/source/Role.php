<?php

/**
 * @file
 * Contains \Drupal\migrate_example\Plugin\migrate\source\Role.
 */

namespace Drupal\pe_migrate_example\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SourcePluginBase;

/**
 * Migrate Example role source from database.
 *
 * @ingroup migrate_example
 *
 * @PluginId("pe_migrate_example_user_role")
 */
class Role extends SourcePluginBase {

  /**
   * Iterator.
   *
   * @var \IteratorIterator
   */
  protected $iterator;

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array('group' => array('type' => 'string'));
  }

  /**
   * {@inheritdoc}
   */
  public function __toString() {
    return (string) $this->query();
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return array(
      'group' => $this->t('Group'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getIterator() {
    if (!isset($this->iterator)) {
      $people = $this->query()->execute()->fetchCol();

      $items = array();
      foreach ($people as $groups) {
        $groups = explode(';', $groups);
        foreach ($groups as $group) {
          if (!isset($items[$group])) {
            $items[$group] = array(
              'group' => $group,
            );
          }
        }
      }
      $this->iterator = new \IteratorIterator(new \ArrayIterator($items));
    }

    return $this->iterator;
  }

  /**
   * {@inheritdoc}
   */
  public function count() {
    $this->getIterator()->count();
  }

  /**
   * Gets the source user records.
   *
   * @returns \Drupal\Core\Database\Query\SelectInterface
   */
  protected function query() {
    return db_select('pe_migrate_example_people', 'p')
      ->fields('p', array('groups'));
  }
}
