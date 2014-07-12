<?php

/**
 * @file
 * Contains \Drupal\migrate_example\Controller\MigrateExampleController.
 */

namespace Drupal\pe_migrate_example\Controller;

/**
 * Controller routines for migrate_example.
 *
 * @ingroup migrate_example
 */
class MigrateExampleController {

  /**
   * A simple controller method to explain what this module is about.
   */
  public function description() {
    $markup = '';

    $markup .= '<p>' . t(' A basic example of defining a migration through configuration YAML files and custom source and process plugins.') . '</p>';
    $markup .= '<p>' . t("In this example we are performing three migrations: roles, users, content. The source of the migration is proprietary hypothetical CMS into Drupal 8, using the migrate API provided by the core Migrate module. The biggest part of the migration is defined in the migration YAML files, placed under <code>config/</code>' directory.") . '</p>';
    $markup .= '<p>' . t("However, migrate config files are not enough because we still need to implement some migration plugins. The module implements a source plugin for each migration and a process plugin for transforming the value to be imported. Sandbox data is defined in migrate_example.install file and it's used in tests provided by the module.") . '</p>';
    $markup .= '<h3>' . t("Migrate examples provided by the module:") . '</h3>';
    $markup .= '<ul>';
    $markup .= '<li>' . t("<code>migrate_example_user_role</code>: Migrates the source groups into Drupal roles. The source doesn't keep a separate table for groups, we are processing them from the people table.") . '</li>';
    $markup .= '<li>' . t("<code>migrate_example_people</code>: Migrates the list of users into Drupal. This migration request a simple custom process plugin 'explode' that expands the list of user groups from a semicolon delimited string to an array.") . '</li>';
    $markup .= '<li>' . t("<code>migrate_example_content</code>: Migrates the source articles into destination node entities of type <code>page</code>. Note that the article author is migrated using the map of the previous users migration.") . '</li>';
    $markup .= '</ul>';
    $markup .= '<h3>' . t("How to test?") . '</h3>';
    $markup .= '<p>' . t("There are three simpletest tests provided for each migration or you can simply enable the module and run manually the migrations.") . '</p>';

    // @todo The 'body' field to be migrated in https://drupal.org/node/2164451.

    return array('#markup' => $markup);
  }

}
