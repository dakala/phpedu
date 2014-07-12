<?php

/**
 * @file
 * Contains \Drupal\pe_group\GroupAutocomplete.
 */

namespace Drupal\pe_group;

use Drupal\Component\Utility\String;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityManager;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\pe_group\GroupManager;

/**
 * Defines a helper class to get user autocompletion results.
 */
class GroupAutocomplete {

  /**
   * The database connection to query for the user names.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * The config factory to get the anonymous user name.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * The entity query factory service.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQuery;

  /**
   * The entity manager service.
   *
   * @var \Drupal\Core\Entity\EntityManager
   */
  protected $entityManager;

  /**
   * Constructs a GroupAutocomplete object.
   *
   * @param \Drupal\Core\Database\Connection $connection
   *   The database connection to query for the user names.
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(Connection $connection, ConfigFactoryInterface $config_factory, EntityManager $entity_manager, QueryFactory $entity_query) {
    $this->connection = $connection;
    $this->configFactory = $config_factory;
    $this->entityQuery = $entity_query;
    $this->entityManager = $entity_manager;
  }

  /**
   * Get matches for the autocompletion of user names.
   *
   * @param string $string
   *   The string to match for usernames.
   *
   * @param bool $include_anonymous
   *   (optional) TRUE if the the name used to indicate anonymous users (e.g.
   *   "Anonymous") should be autocompleted. Defaults to FALSE.
   *
   * @return array
   *   An array containing the matching usernames.
   */
  public function getMatches($string) {
    $matches = array();
    $groups = \Drupal::service('group.manager')->getAllGroups();
    foreach ($groups as $group) {
      if (stripos($group->label(), $string) !== FALSE) {
        $matches[] = array('value' => $group->label(), 'label' => String::checkPlain($group->label()));
      }
    }
    return $matches;
  }

}
