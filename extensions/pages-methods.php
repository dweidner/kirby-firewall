<?php

/**
 * @file
 * Extending Kirby’s page collection
 *
 * @author  Daniel Weidner <hallo@danielweidner>
 * @package Kirby\Plugin\Firewall
 */

/**
 * Return all pages from the collection that are accessible by the given
 * user/role.
 *
 * @since   1.0.0
 *
 * @param \Pages $pages Collection of pages.
 * @param \User|\Role $obj User or role object.
 * @return \Pages
 */
$kirby->set('pages::method', 'accessibleBy', function($pages, $obj) {
  return $pages->filter(function($page) use ($obj) {
    return $page->isAccessibleBy($obj);
  });
});

/**
 * Return all pages from the collection that the currently logged-in user
 * is allowed to access.
 *
 * @since   1.1.0
 *
 * @param \Pages $pages Collection of pages.
 * @return \Pages
 */
$kirby->set('pages::method', 'accessibleByCurrentUser', function($pages) {
  $user = site()->user();
  return $pages->filter(function($page) use ($user) {
    return $page->isAccessibleBy($user);
  });
});

/**
 * Return all pages from the collection that the given user/role
 * is not allowed to access.
 *
 * @since   1.0.0
 *
 * @param \Pages $pages Collection of pages.
 * @param \User|\Role $obj User or role object.
 * @return \Pages
 */
$kirby->set('pages::method', 'inaccessibleBy', function($pages, $obj) {
  return $pages->filter(function($page) use ($obj) {
    return !$page->isAccessibleBy($obj);
  });
});

/**
 * Return all pages from the collection the currently logged-in user
 * is not allowed to access.
 *
 * @since   1.0.0
 *
 * @param \Pages $pages Collection of pages.
 * @return \Pages
 */
$kirby->set('pages::method', 'inaccessibleByCurrentUser', function($pages) {
  $user = site()->user();
  return $pages->filter(function($page) use ($user) {
    return !$page->isAccessibleBy($user);
  });
});
