<?php

/**
 * @file
 * Plugin routes
 *
 * @author  Daniel Weidner <hallo@danielweidner>
 * @package Kirby\Plugin\Firewall
 * @since   1.0.0
 */

/**
 * File Access Control
 *
 * A custom route that prevents users with insufficient permissions from
 * accessing page assets.
 *
 * @see https://getkirby.com/docs/cookbook/asset-firewall How to build an asset firewall
 */
$kirby->set('route', [
  'pattern' => 'content/(.*)',
  'action'  => function($path) {
    $directories = str::split($path, '/');
    $filename = array_pop($directories);

    $page = site();
    $user = site()->user();

    foreach ($directories as $dirname) {
      if ($child = $page->children()->findBy('dirname', $dirname)) {
        if ($child->isAccessibleBy($user)) {
          $page = $child;
        } else {
          header::forbidden();
          die('Access denied');
        }
      } else {
        header::notFound();
        die('Page not found');
      }
    }

    if ($file = $page->file($filename)) {
      $file->show();
    } else {
      header::notFound();
      die('File not found');
    }
  },
]);

/**
 * Page Access Control
 *
 * A custom route that prevents users with insufficient permissions from
 * accessing certain pages.
 *
 * @see https://getkirby.com/docs/developer-guide/advanced/routing Advanced tasks: Routing
 */
$kirby->set('route', [
  'pattern' => '(.*)',
  'action'  => function($uid) {
    $page = ($uid === '/') ? site()->homePage() : page($uid);
    $user = site()->user();

    if (!$page) {
      return site()->visit(site()->errorPage());
    }

    if (!$page->isAccessibleBy($user)) {
      if ($redirect = c::get('firewall.redirect')) {
        go($redirect);
      } else {
        header::forbidden();
        die('Access denied');
      }
    }

    return site()->visit($page);
  }
]);
