<?php

/**
 * @file
 * Extending Kirby’s file object
 *
 * @author  Daniel Weidner <hallo@danielweidner>
 * @package Kirby\Plugin\Firewall
 * @since   1.0.0
 */

/**
 * Check whether acces to the given file is restricted to specific users or
 * roles only.
 *
 * @param \File $file File to test.
 * @return bool
 */
$kirby->set('file::method', 'isAccessRestricted', function($file) {
  return $file->page()->isAccessRestricted();
});

/**
 * Check whether the given file is accessible by the currently logged-in user.
 *
 * @param \File $file File to test.
 * @return bool
 */
$kirby->set('file::method', 'isAccessible', function($file) {
  return $file->page()->isAccessibleBy(site()->user());
});

/**
 * Check whether the file is accessible by a certain user or role.
 *
 * @param \File $file File to test.
 * @param \User|\Role $obj Name of a user/role or the corresponding instances.
 * @return bool
 */
$kirby->set('file::method', 'isAccessibleBy', function($file, $obj) {
  return $file->page()->isAccessibleBy($obj);
});
