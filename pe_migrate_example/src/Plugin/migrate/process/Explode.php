<?php

/**
 * @file
 * Contains \Drupal\migrate_example\Plugin\migrate\process\Explode.
 */

namespace Drupal\pe_migrate_example\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Explodes a strings by a delimiter.
 *
 * @ingroup migrate_example
 *
 * @MigrateProcessPlugin(
 *   id = "explode",
 * )
 */
class Explode extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutable $migrate_executable, Row $row, $destination_property) {
    return explode($this->configuration['delimiter'], $value);
  }

}
