<?php

/**
 * @file
 * Contains \Drupal\migrate_example\Plugin\migrate\source\People.
 */

namespace Drupal\pe_migrate_example\Plugin\migrate\source;

/**
 * Migrate Example role source from database.
 *
 * @ingroup migrate_example
 *
 * @PluginId("pe_migrate_example_people")
 */
class People extends Base {

  /**
   * {@inheritdoc}
   */
  public function getIds() {
    return array('id' => array('type' => 'integer'));
  }

  /**
   * {@inheritdoc}
   */
  public function fields() {
    return array(
      'id' => $this->t('User ID'),
      'first_name' => $this->t('First name'),
      'last_name' => $this->t('Last name'),
      'email' => $this->t('E-mail'),
      'pass' => $this->t('MD5 hashed password'),
      'groups' => $this->t('User groups'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    return $this->select('migrate_example_people', 'p')
      ->fields('p', array('id', 'first_name', 'last_name', 'email', 'pass', 'groups'));
  }

}
