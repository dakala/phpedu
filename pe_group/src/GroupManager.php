<?php
/**
 * @file
 * Contains \Drupal\pe_group\GroupManager.
 */

namespace Drupal\pe_group;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Language\Language;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\StringTranslation\TranslationInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\Entity\Term;

/**
 * Group Manager Service.
 */
class GroupManager {

  /**
   * Database Service Object.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $connection;

  /**
   * Entity manager Service Object.
   *
   * @var \Drupal\Core\Entity\EntityManagerInterface
   */
  protected $entityManager;

  /**
   * The translation service.
   *
   * @var \Drupal\Core\StringTranslation\TranslationInterface
   */
  protected $translation;

  /**
   * Config Factory Service Object.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Groups Array.
   *
   * @var array
   */
  protected $groups;

  /**
   * Fields Array.
   *
   * @var array
   */
  protected $fields;

  protected $terms;

  /**
   * Constructs a BookManager object.
   */
  public function __construct(Connection $connection, EntityManagerInterface $entity_manager, TranslationInterface $translation, ConfigFactoryInterface $config_factory) {
    $this->connection = $connection;
    $this->entityManager = $entity_manager;
    $this->translation = $translation;
    $this->configFactory = $config_factory;
  }

  /**
   * Returns an array of all groups.
   *
   * This list may be used for generating a list of all the groups, or for building
   * the options for a form select.
   *
   * @return
   *   An array of all groups.
   */
  public function getAllGroups() {
    if (!isset($this->groups)) {
      $this->loadGroups();
    }
    return $this->groups;
  }

  /**
   * Loads array of groups.
   */
  protected function loadGroups() {
    $this->groups = array();
    $terms = $this->getAllGroupTerms();
    if ($terms) {
      foreach ($terms as $term) {
        $vocabulary = entity_load('taxonomy_vocabulary', $term->vid->value);
        $term->vocabulary = $vocabulary->label();
        $term->posts = $this->countTermEntities($term, 'node');
        $term->members = $this->countTermEntities($term, 'user');
        $this->groups[] = $term;
      }
    }
  }

  public function getAllGroupTerms() {
    if (!isset($this->terms)) {
      $this->loadGroupTerms();
    }
    return $this->terms;
  }

  protected function loadGroupTerms() {
    $this->terms = array();
    $group_vids = $this->getAllGroupVocabularies();
    if ($group_vids) {
      foreach ($group_vids as $vid) {
        $tree = taxonomy_get_tree($vid, 0, NULL, TRUE);
        foreach ($tree as $term) {
          $this->terms[] = $term;
        }
      }
    }
  }

  /**
   * Get all group vocabularies which set an entity apart as a group.
   *
   * @return array
   */
  public function getAllGroupVocabularies() {
//    $group_vids = array();
//    $vids = \Drupal::config('pe_group.settings')->get('group_vocabulary');
//    if ($vids) {
//      foreach ($vids as $vid => $enabled) {
//        if ($enabled) {
//          $group_vids[] = $vid;
//        }
//      }
//    }
//    return $group_vids;

    return \Drupal::config('pe_group.settings')->get('vocabulary');
  }

  /**
   * Count all entities tagged with a term.
   *
   * @param $term
   * @param $entity_type
   *
   * @return int
   */
  public function countTermEntities($term, $entity_type) {
    $entities = $this->getTermEntities($term, $entity_type);
    return ($entities) ? count($entities) : 0;
  }

  /**
   * Get all entities tagged with a term.
   *
   * @param $field
   * @param $term
   *
   * @return array
   */
  public function getTermEntities($term, $entity_type) {
    $tid = ($term instanceof \Drupal\taxonomy\Entity\Term) ? $term->id() : $term->tid;
    $field = $this->loadTermReferenceField($term, $entity_type);
    if (!$field) {
      return array();
    }
    $table = str_replace('.', '__', $field->id);
    $field = $field->name . '_target_id';
    $query = $this->connection->query("SELECT entity_id FROM {$table} WHERE $field = :tid", array(':tid' => $tid));
    return $query->fetchAll();
  }

  /**
   * Get all entity ids tagged with a term.
   *
   * @param $term
   * @param $entity_type
   *
   * @return array
   */
  public function getTermEntityIds($term, $entity_type) {
    $entity_ids = array();
    $entities = $this->getTermEntities($term, $entity_type);
    if (count($entities)) {
      foreach ($entities as $entity) {
        $entity_ids[] = $entity->entity_id;
      }
    }
    return $entity_ids;
  }


  /**
   * Read term reference field for a term and entity type - node or user
   *
   * @param $term
   *  The array of term definition.
   * @param $entity_type
   *  The entity type to match for the field - node or user
   *
   * @return mixed
   */
  public function loadTermReferenceField($term, $entity_type) {
    $vid = ($term instanceof Term) ? $term->vid->value : $term->vid;
    $fields = $this->loadTermReferenceFields();
    foreach ($fields as $field) {
      // A term reference field can only reference one vocabulary.
      if (($field->settings['allowed_values'][0]['vocabulary'] == $vid) &&
        ($field->entity_type == $entity_type)
      ) {
        return $field;
      }
    }
  }

  /**
   * Read all term reference fields which mark an entity as a group.
   *
   * @return array
   */
  public function loadTermReferenceFields() {
    if (!isset($this->fields)) {
      $fields = $this->entityManager->getStorage('field_storage_config')
        ->loadMultiple();

      foreach ($fields as $key => $field) {
        if ($field->type != 'taxonomy_term_reference') {
          unset($fields[$key]);
        }
      }
      $this->fields = $fields;
    }
    return $this->fields;
  }

  /**
   * Get all group terms that this entity type can be tagged with.
   *
   * @param $entity_type
   *
   * @return array
   */
  public function loadGroupEntityReferenceFields($entity_type) {
    $group_vids = $this->getAllGroupVocabularies();
    $fields = $this->loadTermReferenceFields();
    $group_fields = array();
    foreach ($fields as $key => $field) {
      // @todo: A term reference field can only reference one vocabulary???
      if ((in_array($field->settings['allowed_values'][0]['vocabulary'], $group_vids)) &&
        ($field->entity_type == $entity_type)
      ) {
        $group_fields[$key] = $field;
      }
    }
    return $group_fields;
  }

  /**
   * Get all terms that identify this entity as a group.
   *
   * @param $entity
   *
   * @return array
   */
  public function getGroupEntityReferenceTids($entity) {
    $groupFields = $this->loadGroupEntityReferenceFields($entity->getEntityTypeId());
    $entityTids = array();
    foreach ($groupFields as $groupField) {
      if (isset($entity->{$groupField->name})) {
        foreach ($entity->{$groupField->name} as $field) {
          if (isset($field->target_id)) {
            $entityTids[] = $field->target_id;
          }
        }
      }
    }
    return $entityTids;
  }

  /**
   * Get all group roles for the given users.
   *
   * @param array $users
   */
  public function getGroupRolesForUsers(array &$users) {
    // get all groups
    $terms = $this->getAllGroupTerms();
    // get all members of each group
    foreach ($terms as $term) {
      // load special roles for this group
      $storage = \Drupal::entityManager()->getStorage('group_role');
      $roles = $storage->getGroupRolesForGroup($term, $users);
      $members = $this->getTermEntities($term, 'user');
      if (count($members)) {
        foreach ($members as $member) {
          $group_roles = array();
          if (isset($users[$member->entity_id])) {
            $group_roles[$term->machine_name][] = GROUP_MEMBER_RID;
            if (isset($roles[$term->machine_name][$member->entity_id])) {
              $group_roles[$term->machine_name] = array_merge($group_roles[$term->machine_name], $roles[$term->machine_name][$member->entity_id]);
            }
            $users[$member->entity_id]->group_roles = $group_roles;
          }
        }
      }
    }
  }

  /**
   * Get assigned group officers.
   *
   * @param $term
   *
   * @return array
   */
  public function getGroupOfficers($term) {
    $officers = array();
    $query = $this->connection->query("SELECT tid, rid, uid FROM {users_group_roles} WHERE tid = :tid AND rid IN (:rids)",
      array(
        ':tid' => $term->id(),
        ':rids' => array(
          GROUP_ADMINISTRATOR_RID,
          GROUP_SECRETARY_RID,
          GROUP_LEADER_RID
        )
      ));
    $result = $query->fetchAll();
    if (count($result)) {
      foreach ($result as $officer) {
        $username = array(
          '#theme' => 'username',
          '#account' => user_load($officer->uid),
          '#link_options' => array('attributes' => array('rel' => 'author')),
        );
        $officers[$officer->rid][] = drupal_render($username);
      }
    }
    return $officers;
  }

  /**
   * Check whether the user has the given role in the given group.
   *
   * 1. user has any group roles.
   * 2. user is a member of the given group.
   * 3. user has the given role in the group.
   *
   * @param $account
   * @param $rid
   * @param $tid
   *
   * @return bool
   */
  public function hasGroupRole($account, $rid, $tid) {


    var_dump($account->id());
    var_dump($rid);
    var_dump($tid);

    return (isset($account->group_roles) && count($account->group_roles) && array_key_exists($tid, array_keys($account->group_roles)) && in_array($rid, $account->group_roles[$tid])) ? TRUE : FALSE;
  }


  public function pe_group_get_id_from_machine_name($machine_name) {
    return db_select('taxonomy_term_machine_name', 'm')
      ->fields('m', array('tid'))
      ->condition('machine_name', $machine_name)
      ->execute()->fetchField();
  }

  /**
   * @todo:
   * @deprecated
   *
   * Translates a string to the current language or to a given language.
   *
   * See the t() documentation for details.
   */
  protected function t($string, array $args = array(), array $options = array()) {
    return $this->translation->translate($string, $args, $options);
  }

  public function getTaxonomyTermChildren($tid, $depth) {
    $tids = array();
    $term = entity_load('taxonomy_term', $tid);
    if ($term instanceof Term) {
      $children = taxonomy_get_tree($term->getVocabularyId(), $term->id(), $depth);
      foreach ($children as $child) {
        $tids[] = (int) $child->tid;
      }
    }
    return $tids;
  }

  public function getTaxonomyTermParents($tid, $depth) {
    $tids = array();
    $parents = taxonomy_term_load_parents_all($tid);
    // first element is the searched term.
    if ((!empty($parents)) && (count($parents) > 1)) {
      array_shift($parents);
      for ($i = 0; $i < $depth; $i++) {
        $parent = array_shift($parents);
        $tids[] = (int) $parent->id();
      }
    }
    return $tids;
  }


}
