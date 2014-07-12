<?php

/**
 * @file
 * Contains \Drupal\migrate_example\Tests\MigrateExamplePeopleTest.
 */

namespace Drupal\migrate_example\Tests;

use Drupal\Component\Utility\Unicode;
use Drupal\migrate\MigrateExecutable;

class MigrateExamplePeopleTest extends MigrateExampleTestBase {

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name'  => 'Migrate Users Example',
      'description'  => 'Example of migrating users from a proprietary legacy system.',
      'group' => 'Examples',
    );
  }

  function testUsers() {

    $migration = entity_load('migration', 'migrate_example_people');
    $executable = new MigrateExecutable($migration, $this);
    $executable->import();

    $source = db_select('migrate_example_people', 'p')
      ->fields('p', array('id', 'first_name', 'last_name', 'email', 'pass', 'plain_pass', 'groups'))
      ->execute()
      ->fetchAllAssoc('id');

    foreach ($source as $id => $person) {
      // Get the destination user ID.
      $uid = $migration->getIdMap()->lookupDestinationId(array($id));
      $uid = reset($uid);

      $user = entity_load('user', $uid);

      // Assertions.
      $name = trim(Unicode::strtolower($person->first_name . '.' . $person->last_name));
      $this->assertEqual($user->label(), $name);
      $this->assertEqual($user->mail->value, $person->email);

      $groups = explode(';', $person->groups);
      $diff = array_diff($user->getRoles(), $groups);
      $this->assertEqual($diff, array('authenticated'));

      // Login using the UI.
      $this->drupalPostForm('user/login', array('name' => $name, 'pass' => $person->plain_pass), t('Log in'));
      $this->assertNoRaw(t('Sorry, unrecognized username or password. <a href="@password">Have you forgotten your password?</a>', array('@password' => url('user/password', array('query' => array('name' => $name))))));
      $this->drupalLogout();
    }

  }

}
