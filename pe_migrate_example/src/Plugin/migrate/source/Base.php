<?php

/**
 * @file
 * Contains \Drupal\migrate_example\Plugin\migrate\source\Base.
 */

namespace Drupal\pe_migrate_example\Plugin\migrate\source;

use Drupal\Core\Database\Database;
use Drupal\migrate\Plugin\migrate\source\SqlBase;

/**
 * Migrate Example base class for users and content sources.
 *
 * @ingroup migrate_example
 */
abstract class Base extends SqlBase {

  /**
   * {@inheritdoc}
   */
  public function getDatabase() {
    if (!isset($this->database)) {
      $this->database = Database::getConnection('default', 'default');
    }
    return $this->database;
  }

}
