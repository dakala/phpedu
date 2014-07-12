<?php

/**
 * @file
 * Contains \Drupal\pe_group\Controller\GroupAutocompleteController.
 */
namespace Drupal\pe_group\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\pe_group\GroupAutocomplete;

/**
 * Controller routines for group routes.
 */
class GroupAutocompleteController implements ContainerInjectionInterface {

  /**
   * The group autocomplete helper class to find matching group names.
   *
   * @var \Drupal\pe_group\GroupAutocomplete
   */
  protected $groupAutocomplete;

  /**
   * Constructs an GroupAutocompleteController object.
   *
   * @param \Drupal\pe_group\GroupAutocomplete $group_autocomplete
   *   The group autocomplete helper class to find matching group names.
   */
  public function __construct(GroupAutocomplete $group_autocomplete) {
    $this->groupAutocomplete = $group_autocomplete;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('group.autocomplete')
    );
  }

  /**
   * Returns response for the group autocompletion.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The current request object containing the search string.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   A JSON response containing the autocomplete suggestions for existing groups.
   *
   * @see \Drupal\pe_group\GroupAutocomplete::getMatches()
   */
  public function autocompleteGroup(Request $request) {
    $matches = $this->groupAutocomplete->getMatches($request->query->get('q'));

    return new JsonResponse($matches);
  }

}
