<?php

/**
 * @file
 * Extending Kirby’s page object
 *
 * @author  Daniel Weidner <hallo@danielweidner>
 * @package Kirby\Plugin\Firewall
 */

/**
 * Check whether the given page has restricted access to specific users or
 * roles only.
 *
 * @since   1.0.0
 *
 * @param \Page $page Page to test.
 * @return bool
 */
$kirby->set('page::method', 'isAccessRestricted', function($page) {
  $field = $page->content()->get(c::get('plugin.firewall.fieldname', 'firewall'));

  if (!$field->exists() || v::accepted($field->value())) {
    return false;
  }

  if (v::denied($field->value())) {
    return true;
  }

  $value = $field->yaml();
  $value = array_filter($value);

  return is_array($value) && !empty($value);
});

/**
 * Check whether the page is accessible by a certain user or role.
 *
 * @since   1.0.0
 *
 * @param \Page $page Page to test.
 * @param \User|\Role $obj User or role object.
 * @return bool
 */
$kirby->set('page::method', 'isAccessibleBy', function($page, $obj) {
  if (!$page->isAccessRestricted()) {
    return true;
  }

  $field = $page->content()->get(c::get('plugin.firewall.fieldname', 'firewall'));

  if (!$obj || v::denied($field->value())) {
    return false;
  }

  $rules = $field->yaml();
  $users = a::get($rules, 'users');
  $roles = a::get($rules, 'roles');

  if ($obj instanceof \Role) {
    return !empty($roles) && in_array($obj->id(), $roles);
  }

  return ( !empty($users) && in_array($obj->username(), $users) ) || ( !empty($roles) && in_array($obj->role()->id(), $roles) );
});

/**
 * Check whether the page is accessible by the currently logged-in user.
 *
 * @since   1.1.0
 *
 * @param \Page $page Page to test.
 * @return bool
 */
$kirby->set('page::method', 'isAccessibleByCurrentUser', function($page) {
  return $page->isAccessibleBy($page->site()->user());
});
