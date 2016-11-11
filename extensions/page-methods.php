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
 * @param \User|\Role $obj Name of a user/role or the corresponding instances.
 * @return bool
 */
$kirby->set('page::method', 'isAccessibleBy', function($page, $obj) {
  if (!$page->isAccessRestricted()) {
    return true;
  }

  $field = $page->content()->get(c::get('plugin.firewall.fieldname', 'access'));

  if (!$obj || v::denied($field->value())) {
    return false;
  }

  $rules = $field->yaml();
  $data  = null;

  if ($obj instanceof \User) {
    $data = ['users' => $obj->username(), 'roles' => $obj->role()->id()];
  } else if ($obj instanceof \Role) {
    $data = ['roles' => $obj->id()];
  }

  if (empty($data)) {
    return false;
  }

  foreach ($rules as $type => $whitelist) {
    if (array_key_exists($type, $data) && in_array($data[$type], $whitelist)) {
      return true;
    }
  }

  return false;

});
