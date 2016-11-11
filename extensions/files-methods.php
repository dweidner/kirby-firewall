<?php

/**
 * @file
 * Extending Kirby’s file collection
 *
 * @author  Daniel Weidner <hallo@danielweidner>
 * @package Kirby\Plugin\Firewall
 * @since   1.0.0
 */

/**
 * Return all files from the collection which are accessible by the currently
 * logged-in user.
 *
 * @param \Files $files Collection of files.
 * @return \Files
 */
$kirby->set('files::method', 'accessible', function($files) {
  return $files->filter(function($file) {
    return $file->isAccessible();
  });
});

/**
 * Return all files from the collection that are accessible by the given
 * user/role.
 *
 * @param \Files $files Collection of files.
 * @param \User|\Role $obj
 * @return \Files
 */
$kirby->set('files::method', 'accessibleBy', function($files, $obj) {
  return $files->filter(function($file) use ($obj) {
    return $file->isAccessibleBy($obj);
  });
});

/**
 * Return all files from the collection that the currently logged-in user
 * is not allowed to access.
 *
 * @param \Files $files Collection of files.
 * @return \Files
 */
$kirby->set('files::method', 'inaccessible', function($files) {
  return $files->filter(function($file) {
    return !$file->isAccessible();
  });
});

/**
 * Return all files from the collection that the given user/role
 * is not allowed to access.
 *
 * @param \Files $files Collection of files.
 * @param \User|\Role $obj
 * @return \Files
 */
$kirby->set('files::method', 'inaccessibleBy', function($files, $obj) {
  return $files->filter(function($file) use ($obj) {
    return !$file->isAccessibleBy($obj);
  });
});
