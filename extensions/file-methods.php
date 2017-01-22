<?php

/**
 * @file
 * Extending Kirby’s file object
 *
 * @author  Daniel Weidner <hallo@danielweidner>
 * @package Kirby\Plugin\Firewall
 */

/**
 * Check whether acces to the given file is restricted to specific users or
 * roles only.
 *
 * @since   1.0.0
 *
 * @param \File $file File to test.
 * @return bool
 */
$kirby->set('file::method', 'isAccessRestricted', function($file) {
  return $file->page()->isAccessRestricted();
});

/**
 * Check whether the file is accessible by a certain user or role.
 *
 * @since   1.0.0
 *
 * @param \File $file File to test.
 * @param \User|\Role $obj User or role object.
 * @return bool
 */
$kirby->set('file::method', 'isAccessibleBy', function($file, $obj) {
  return $file->page()->isAccessibleBy($obj);
});

/**
 * Check whether the file is accessible by the currently logged-in user.
 *
 * @since   1.1.0
 *
 * @param \File $file File to test.
 * @return bool
 */
$kirby->set('file::method', 'isAccessibleByCurrentUser', function($file) {
  $page = $file->page();
  return $page->isAccessibleBy($page->site()->user());
});
