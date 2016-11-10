<?php

/**
 * @file
 * Extending Kirby’s page collection
 *
 * @author  Daniel Weidner <hallo@danielweidner>
 * @package Kirby\Plugin\Firewall
 * @since   1.0.0
 */

/**
 * Return all pages from the collection which are accessible by the currently
 * logged-in user.
 *
 * @param \Pages $pages Collection of pages.
 * @return \Pages
 */
$kirby->set('pages::method', 'accessible', function($pages) {
  return $pages->filter(function($page) {
    return $page->isAccessible();
  });
});

/**
 * Return all pages from the collection that are accessible by the given
 * user/role.
 *
 * @param \Pages $pages Collection of pages.
 * @param \User|\Role|string $obj
 * @return \Pages
 */
$kirby->set('pages::method', 'accessibleBy', function($pages, $obj) {
  return $pages->filter(function($page) use ($obj) {
    return $page->isAccessibleBy($obj);
  });
});

/**
 * Return all pages from the collection that the currently logged-in user
 * is not allowed to access.
 *
 * @param \Pages $pages Collection of pages.
 * @return \Pages
 */
$kirby->set('pages::method', 'inaccessible', function($pages) {
  return $pages->filter(function($page) {
    return !$page->isAccessible();
  });
});

/**
 * Return all pages from the collection that the given user/role
 * is not allowed to access.
 *
 * @param \Pages $pages Collection of pages.
 * @param \User|\Role|string $obj
 * @return \Pages
 */
$kirby->set('pages::method', 'inaccessibleBy', function($pages, $obj) {
  return $pages->filter(function($page) use ($obj) {
    return !$page->isAccessibleBy($obj);
  });
});
