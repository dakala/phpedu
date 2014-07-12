<?php

/**
 * @file
 * Contains \Drupal\migrate_example\Tests\MigrateExampleUserRoleTest.
 */

namespace Drupal\migrate_example\Tests;

use Drupal\migrate\MigrateExecutable;

class MigrateExampleUserRoleTest extends MigrateExampleTestBase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name'  => 'Migrate User Roles Example',
      'description'  => 'Example of migrating user groups/roles from a proprietary legacy system.',
      'group' => 'Examples',
    );
  }

  function testUserRole() {

    /** @var \Drupal\migrate\entity\Migration $migration */
    $migration = entity_load('migration', 'migrate_example_user_role');
    $executable = new MigrateExecutable($migration, $this);
    $executable->import();

    foreach (array('staff', 'management', 'shareholder') as $rid) {
      $role = entity_load('user_role', $rid);
      $this->assertEqual($role->id(), $rid);
      $this->assertEqual(array($rid), $migration->getIdMap()->lookupDestinationId(array($rid)));
    }
  }

}
