<?php

/**
 * @file
 * Extending Kirby’s page collection
 *
 * @author  Daniel Weidner <hallo@danielweidner.de>
 * @package Kirby\Plugin\Firewall
 * @since   1.0.1
 */

/**
 * Return all pages from the collection that are accessible by the given
 * user/role.
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
 * Return all pages from the collection that the given user/role
 * is not allowed to access.
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
