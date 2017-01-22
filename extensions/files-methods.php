<?php

/**
 * @file
 * Extending Kirby’s file collection
 *
 * @author  Daniel Weidner <hallo@danielweidner>
 * @package Kirby\Plugin\Firewall
 */

/**
 * Return all files from the collection that are accessible by the given user.
 *
 * @since   1.0.0
 *
 * @param \Files $files Collection of files.
 * @param \User|\Role $obj User or role object.
 * @return \Files
 */
$kirby->set('files::method', 'accessibleBy', function($files, $obj) {
  return $files->filter(function($file) use ($obj) {
    return $file->isAccessibleBy($obj);
  });
});

/**
 * Return all files from the collection that are accessible by the currently
 * logged-in user.
 *
 * @since   1.0.0
 *
 * @param \Files $files Collection of files.
 * @return \Files
 */
$kirby->set('files::method', 'accessibleByCurrentUser', function($files) {
  $user = site()->user();
  return $files->filter(function($file) use ($user) {
    return $file->isAccessibleBy($user);
  });
});

/**
 * Return all files from the collection that the given user is not allowed
 * to access.
 *
 * @since   1.0.0
 *
 * @param \Files $files Collection of files.
 * @param \User|\Role $obj User or role object.
 * @return \Files
 */
$kirby->set('files::method', 'inaccessibleBy', function($files, $obj) {
  return $files->filter(function($file) use ($obj) {
    return !$file->isAccessibleBy($obj);
  });
});

/**
 * Return all files from the collection that the currently logged-in user is
 * not allowed to access.
 *
 * @since   1.1.0
 *
 * @param \Files $files Collection of files.
 * @return \Files
 */
$kirby->set('files::method', 'inaccessibleByCurrentUser', function($files) {
  $user = site()->user();
  return $files->filter(function($file) use ($user) {
    return !$file->isAccessibleBy($user);
  });
});
