<?php

/**
 * @file
 * Contains \Drupal\pe_group\Controller\GroupController.
 */

namespace Drupal\pe_group\Controller;

use Drupal\pe_group\GroupManager;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Drupal\Core\Entity\EntityStorageControllerInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\taxonomy\VocabularyInterface;
use Drupal\taxonomy\Controller\TaxonomyController;
use Drupal\Component\Utility\Xss;
use Drupal\pe_group\GroupRoleStorageInterface;
use Drupal\views\Views;

/**
 * Controller routines for group routes.
 */
class GroupController implements ContainerInjectionInterface {

  /**
   * The group manager.
   *
   * @var \Drupal\pe_group\GroupManager
   */
  protected $groupManager;

  /**
   * The group role storage.
   *
   * @var \Drupal\user\GroupRoleStorageInterface
   */
  protected $roleStorage;

  /**
   * Constructs a GroupController object.
   *
   * @param \Drupal\pe_group\GroupManager $groupManager
   *   The group manager.
   * @param \Drupal\pe_group\GroupExport $groupExport
   *   The group export service.
   */
  public function __construct(GroupManager $groupManager, GroupRoleStorageInterface $role_storage) {
    $this->groupManager = $groupManager;
    $this->roleStorage = $role_storage;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('group.manager'),
      $container->get('entity.manager')->getStorage('group_role')
    );
  }

  /**
   * Returns the list of groups.
   *
   * @return mixed
   */
  public function groupIndex() {
    $header = array(
      t('Group'),
      t('Type'),
      t('Members'),
      t('Posts')
    );

    $rows = array();
    $groups = $this->groupManager->getAllGroups();
    if (count($groups)) {
      $form['#attached']['library'][] = 'pe_group/pe_group.groups.list';

      $form['filters'] = array(
        '#type' => 'container',
        '#attributes' => array(
          'class' => array('table-filter', 'js-show'),
        ),
      );

      $form['filters']['text'] = array(
        '#type' => 'search',
        '#title' => t('Search'),
        '#size' => 30,
        '#placeholder' => t('Enter group type'),
        '#attributes' => array(
          'class' => array('groups-table-filter-text'),
          'data-table' => '.groups-listing-table',
          'autocomplete' => 'off',
          'title' => t('Enter a part of the group type to filter by.'),
        ),
      );

      foreach ($groups as $group) {
        $links = array();
        $links['edit'] = array(
          'title' => t('Edit'),
          'route_name' => 'taxonomy.term_edit',
          'route_parameters' => array('taxonomy_term' => $group->id()),
        );
        $links['members'] = array(
          'title' => t('Members'),
          'route_name' => 'group.members_list',
          'route_parameters' => array('taxonomy_term' => $group->id()),
        );
        $links['posts'] = array(
          'title' => t('Posts'),
          'route_name' => 'group.posts',
          'route_parameters' => array('taxonomy_term' => $group->id()),
        );

        $row = array(
          'data' => array(
            'name' => array(
              'data' => array(
                '#theme' => 'groups_group_info',
                '#group' => $group
              )
            ),
            'description' => array(
              'data' => array(
                '#theme' => 'groups_group_type',
                '#group' => $group
              )
            ),
            'members' => array('data' => array('#markup' => $group->members)),
            'posts' => array('data' => array('#markup' => $group->posts))
          ),
        );
        if (count($links)) {
          $row['data']['operations'] = array(
            'data' => array(
              '#type' => 'operations',
              '#links' => $links
            )
          );
        }
        $rows[] = $row;
      }
    }

    if (isset($links) && count($links)) {
      $header[] = t('Operations');
    }

    $form['groups_list_table'] = array(
      '#theme' => 'table',
      '#attributes' => array(
        'class' => array('groups-listing-table'),
      ),
      '#header' => $header,
      '#rows' => $rows,
      '#empty' => t('No groups available.'),
    );
    return $form;
  }


  public function groupPage(TermInterface $taxonomy_term) {
    return $this->taxonomyTermPage($taxonomy_term);
  }

  public function termTitle(TermInterface $taxonomy_term) {
    return Xss::filter($taxonomy_term->label());
  }

  public function taxonomyTermPage(TermInterface $term) {
    foreach ($term->uriRelationships() as $rel) {
      // Set the term path as the canonical URL to prevent duplicate content.
      $build['#attached']['drupal_add_html_head_link'][] = array(
        array(
          'rel' => $rel,
          'href' => $term->url($rel),
        ),
        TRUE,
      );

      if ($rel == 'canonical') {
        // Set the non-aliased canonical path as a default shortlink.
        $build['#attached']['drupal_add_html_head_link'][] = array(
          array(
            'rel' => 'shortlink',
            'href' => $term->url($rel, array('alias' => TRUE)),
          ),
          TRUE,
        );
      }
    }

    $build['panel'] = array(
      '#theme' => 'groups_group_summary',
      '#term' => $term,
    );

    $build['taxonomy_terms'] = taxonomy_term_view_multiple(array($term->id() => $term));

    if ($nids = taxonomy_select_nodes($term->id(), FALSE, \Drupal::config('node.settings')
      ->get('items_per_page'))
    ) {

      $build['nodes'] = node_view_multiple(node_load_multiple($nids));
    }
    else {
      $build['no_content'] = array(
        '#prefix' => '<p>',
        '#markup' => t('There is currently no content for this group.'),
        '#suffix' => '</p>',
      );
    }
    return $build;
  }

  public function groupMembers() {
    //return Views::getView('user_admin_people');
    return views_embed_view('user_admin_people');
  }

}
