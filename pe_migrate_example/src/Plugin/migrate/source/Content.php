<?php

/**
 * @file
 * Contains \Drupal\migrate_example\Plugin\migrate\source\Content.
 */

namespace Drupal\pe_migrate_example\Plugin\migrate\source;

/**
 * Migrate Example content source from database.
 *
 * @ingroup migrate_example
 *
 * @PluginId("pe_migrate_example_content")
 */
class Content extends Base {

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array('article_id' => array('type' => 'integer'));
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return array(
      'article_id' => $this->t('Article ID'),
      'subject' => $this->t('Subject'),
      'text' => $this->t('Article body text'),
      'author' => $this->t('Author user ID.'),
      'date' => $this->t('Article release date'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    $fields = array_keys($this->fields());
    return $this->select('migrate_example_content', 'c')
      ->fields('c', $fields);
  }

}
