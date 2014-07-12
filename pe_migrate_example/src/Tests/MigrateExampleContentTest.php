<?php

/**
 * @file
 * Contains \Drupal\migrate_example\Tests\MigrateExampleContentTest.
 */

namespace Drupal\migrate_example\Tests;

use Drupal\migrate\MigrateExecutable;

class MigrateExampleContentTest extends MigrateExampleTestBase {

  /**
   * We are running this test with the 'standard' profile because we need the
   * 'page' node-type as destination for content migration.
   *
   * @var string
   */
  protected $profile = 'standard';

  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return array(
      'name'  => 'Migrate Content Example',
      'description'  => 'Example of migrating content from a proprietary legacy system.',
      'group' => 'Examples',
    );
  }

  function testContent() {
    // Migrate users prior to content migration.
    $user_migration = entity_load('migration', 'migrate_example_people');
    $executable = new MigrateExecutable($user_migration, $this);
    $executable->import();

    // Migrate content.
    $migration = entity_load('migration', 'migrate_example_content');
    $executable = new MigrateExecutable($migration, $this);
    $executable->import();

    $source = db_select('migrate_example_content', 'c')
      ->fields('c', array('article_id', 'subject', 'text', 'author', 'date'))
      ->execute()
      ->fetchAllAssoc('article_id');

    foreach ($source as $article_id => $content) {
      // Get the destination node ID from the migration map.
      $nid = $migration->getIdMap()->lookupDestinationId(array($article_id));
      $nid = reset($nid);

      $node = node_load($nid);

      // Assertions.
      $this->assertEqual($node->title->value, $content->subject);
      $this->assertEqual($node->body->value, $content->text);
      $this->assertEqual($node->getCreatedTime(), strtotime($content->date));

      $uid = $user_migration->getIdMap()->lookupDestinationId(array($content->author));
      $uid = reset($uid);
      $this->assertEqual($node->uid->value, $uid);
    }

  }

}
