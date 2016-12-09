<?php

/**
 * @file
 * Extending Kirby’s file object
 *
 * @author Daniel Weidner <hallo@danielweidner.de>
 * @package Kirby CMS
 * @subpackage Firewall
 * @since 1.0.0
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
 * Check whether the file is accessible by a certain user or role.
 *
 * @param \File $file File to test.
 * @param \User|\Role $obj User or role object.
 * @return bool
 */
$kirby->set('file::method', 'isAccessibleBy', function($file, $obj) {
  return $file->page()->isAccessibleBy($obj);
});
