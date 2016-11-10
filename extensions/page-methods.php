<?php

/**
 * @file
 * Extending Kirby’s page object
 *
 * @author  Daniel Weidner <hallo@danielweidner>
 * @package Kirby\Plugin\Firewall
 * @since   1.0.0
 */

/**
 * Check whether the given page has restricted access to specific users or
 * roles only.
 *
 * @param \Page $page Page to test.
 * @return bool
 */
$kirby->set('page::method', 'isAccessRestricted', function($page) {
  $field = $page->content()->get(c::get('plugin.firewall.fieldname', 'access'));

  if (!$field->exists() || $field->isEmpty()) {
    return false;
  }

  $value = $field->yaml();
  $type = a::get($value, 'type', 'public');
  $list = a::get($value, $type, []);

  return ( $type !== 'public' ) && ( is_array($list) && count($list) > 0 );
});

/**
 * Check whether the given page is accessible by the currently logged-in user.
 *
 * @param \Page $page Page to test.
 * @return bool
 */
$kirby->set('page::method', 'isAccessible', function($page) {
  return $page->isAccessibleBy(site()->user());
});

/**
 * Check whether the page is accessible by a certain user or role.
 *
 * @param \Page $page Page to test.
 * @param \User|\Role|string $obj Name of a user/role or the corresponding instances.
 * @return bool
 */
$kirby->set('page::method', 'isAccessibleBy', function($page, $obj) {
  if (!$page->isAccessRestricted()) {
    return true;
  }

  if (!$obj) {
    return false;
  }

  $field = $page->content()->get(c::get('plugin.firewall.fieldname', 'access'));
  $value = $field->yaml();

  $type = a::get($value, 'type');
  $list = a::get($value, $type, []);

  $needle = null;

  if (is_string($obj) && in_array($type, ['users', 'roles'])) {
    $needle = $obj;
  } else if ($type === 'users' && $obj instanceof \User) {
    $needle = $obj->username();
  } else if ($type === 'roles' && $obj instanceof \User) {
    $needle = $obj->role()->id();
  } else if ($type === 'roles' && $obj instanceof \Role) {
    $needle = $obj->id();
  }

  return $needle && in_array($needle, $list);
});
