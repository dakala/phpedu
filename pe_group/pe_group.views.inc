<?php

/**
 * @file
 * Provide views data for pe_group.module.
 */

/**
 * Implements hook_views_data_alter().
 */
function pe_group_views_data_alter(&$data) {

  $data['node_field_data']['groupstatus'] = array(
    'title' => t('Group status'),
    'help' => t('Whether or not the content is a group.'),
    'field' => array(
      'id' => 'groupstatus',
      'output formats' => array(
        'groupstatus' => array(t('Group'), t('Not group')),
      ),
    ),
  );

  $data['node_field_data']['group_node_vid'] = array(
    'field' => array(
      'title' => t('Group node VID'),
      'help' => t('The VID of a group node.'),
      'id' => 'group_node_vid',
    ),
    'filter' => array(
      'title' => t('Group node VID'),
      'help' => t('The VID of a group node.'),
      'id' => 'group_node_vid',
    )
  );

  $data['taxonomy_term_data']['group_term_vid'] = array(
    'field' => array(
      'title' => t('Group term VID'),
      'help' => t('The VID of a term node.'),
      'id' => 'group_term_vid',
    ),
    'filter' => array(
      'title' => t('Group term VID'),
      'help' => t('The VID of a term node.'),
      'id' => 'group_term_vid',
    )
  );

  $data['taxonomy_term_data']['postcount'] = array(
    'field' => array(
      'title' => t('Total group posts'),
      'help' => t('The total number of posts for a group.'),
      'id' => 'postcount',
    ),
  );

  $data['taxonomy_term_data']['membercount'] = array(
    'field' => array(
      'title' => t('Total group members'),
      'help' => t('The total number of members for a group.'),
      'id' => 'membercount',
    ),
  );

  // users
  $data['users']['tid'] = array(
    'group' => t('User'),
    'title' => t('Has taxonomy term ID (User)'),
    'help' => t('Display user if they have the selected taxonomy terms.'),
    'argument' => array(
      'id' => 'taxonomy_user_tid',
      'name table' => 'users',
      'name field' => 'name',
      'empty field name' => t('Uncategorized'),
      'numeric' => TRUE,
    ),
//    'filter' => array(
//      'title' => t('Has taxonomy term'),
//      'id' => 'taxonomy_user_tid',
//      'numeric' => TRUE,
//      'allow empty' => TRUE,
//    ),
  );
}
