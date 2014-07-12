<?php

/**
 * @file
 * Contains \Drupal\migrate_example\Tests\MigrateExampleTestBase.
 */

namespace Drupal\migrate_example\Tests;

use Drupal\migrate\MigrateMessageInterface;
use Drupal\simpletest\WebTestBase;

class MigrateExampleTestBase extends WebTestBase implements MigrateMessageInterface  {

  public static $modules = array('migrate_example');

  /**
   * {@inheritdoc}
   */
  public function display($message, $type = 'status') {
    if ($type == 'status') {
      $this->pass($message);
    }
    else {
      $this->fail($message);
    }
  }

}
