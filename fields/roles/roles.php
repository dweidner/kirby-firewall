<?php

/**
 * User Roles Field
 *
 * Custom field that displays all user roles of a site as a list
 * of checkboxes.
 *
 * @author     Daniel Weidner <hallo@danielweidner.de>
 * @package    Kirby\Plugin\Firewall
 * @subpackage RolesField
 * @since      1.0.0
 */
class RolesField extends CheckboxesField {

  /**
   * Version of the field.
   *
   * @var string
   */
  const VERSION = '1.1.1';

  /**
   * Name of the custom field. Represents the identifier users have to use
   * within their blueprints.
   *
   * @var string
   */
  const FIELDNAME = 'roles';

  /**
   * Name of roles to exclude from display.
   *
   * @var array<string>
   */
  public $exclude = [];

  /**
   * Get the id of the current field instance.
   *
   * @return string
   */
  public function id() {

    $prefix = '';

    if (is_a($this->parentField, 'BaseField')) {
      $prefix .= $this->parentField->id() . '-';
    }

    return $prefix . parent::id();

  }

  /**
   * Get the name of the current field instance.
   *
   * @return string
   */
  public function name() {

    $prefix = '';

    if (is_a($this->parentField, 'BaseField')) {
      $prefix .= $this->parentField->name() . '-';
    }

    return $prefix . parent::name();

  }

  /**
   * Generate an option element for each registered user.
   *
   * @return array<string,string>
   */
  public function options() {

    $template = c::get('field.' . self::FIELDNAME . '.template', '{id} ({name})');
    $options  = [];

    foreach ($this->roles() as $role) {
      $options[$role->id()] = str::template($template, $role->toArray());
    }

    return $options;

  }

  /**
   * Retrieve a list of user roles. Exclude those roles listed in the
   * blacklist.
   *
   * @return \Roles
   */
  protected function roles() {

    $roles = site()->roles();

    if (!empty($this->exclude)) {
      $roles = call([ $roles, 'not' ], $this->exclude);
    }

    return $roles;

  }

}

