<?php

/**
 * Users Field
 *
 * Custom field that displays all registered users of a site as a list
 * of checkboxes.
 *
 * @author Daniel Weidner <hallo@danielweidner.de>
 * @package Kirby CMS
 * @subpackage Firewall
 * @since 1.0.0
 */
class UsersField extends CheckboxesField {

  /**
   * Version of the field.
   *
   * @var string
   */
  const VERSION = '1.0.0';

  /**
   * Name of the custom field. Represents the identifier users have to use
   * within their blueprints.
   *
   * @var string
   */
  const FIELDNAME = 'users';

  /**
   * Name of users to exclude from display.
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
   * Customize the label and add a direct link to the user overview.
   *
   * @return \Brick
   */
  public function label() {

    $label = parent::label();

    if (panel()->user()->can('panel.user.create')) {
      $button = brick('a')
        ->addClass('structure-add-button label-option')
        ->attr('href', purl('users/add'))
        ->html('<i class="icon icon-left fa fa-plus-circle"></i>' . l('add'));
      $label->append($button);
    }

    return $label;

  }

  /**
   * Generate an option element for each registered user.
   *
   * @return array<string,string>
   */
  public function options() {

    $template = c::get('field.' . self::FIELDNAME . '.template', '{username} ({role})');
    $options  = [];

    foreach ($this->users() as $user) {
      $options[$user->username()] = str::template($template, $user->toArray());
    }

    return $options;

  }

  /**
   * Retrieve a list of registered users. Exclude those users listed in the
   * blacklist.
   *
   * @return \Users
   */
  protected function users() {

    $users = site()->users();

    if (!empty($this->exclude)) {
      $users = call([ $users, 'not' ], $this->exclude);
    }

    return $users;

  }

}
