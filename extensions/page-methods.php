<?php

/**
 * @file
 * Extending Kirby’s page object
 *
 * @author Daniel Weidner <hallo@danielweidner.de>
 * @package Kirby CMS
 * @subpackage Firewall
 * @since 1.0.0
 */

/**
 * Check whether the given page has restricted access to specific users or
 * roles only.
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
