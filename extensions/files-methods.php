<?php

/**
 * @file
 * Extending Kirby’s file collection
 *
 * @author  Daniel Weidner <hallo@danielweidner.de>
 * @package Kirby\Plugin\Firewall
 * @since   1.0.1
 */

/**
 * Return all files from the collection that are accessible by the given user.
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
 * Return all files from the collection that the given user is not allowed
 * to access.
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
