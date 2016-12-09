<?php

/**
 * Firewall Field
 *
 * Custom field  to protect your pages and files from unauthorized access.
 *
 * @author Daniel Weidner <hallo@danielweidner.de>
 * @package Kirby CMS
 * @subpackage Firewall
 * @since 1.0.0
 */
class FirewallField extends BaseField {

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
  const FIELDNAME = 'firewall';

  /**
   * Assets to load by the Kirby Panel.
   *
   * @var array<string,array>
   */
  public static $assets = [
    'js' => [
      'script.js',
    ],
    'css' => [
      'style.css',
    ],
  ];

  /**
   * A list of possible entity types the user can select from.
   *
   * @var array<string>
   */
  public static $fields = [
    'roles',
    'users',
  ];

  /**
   * Allow to display the list items of the child controls in multiple columns.
   *
   * @var int
   */
  public $columns = 2;

  /**
   * Name of users or roles that should be excluded from display.
   *
   * @var array<string,array>
   */
  public $exclude = [
    'roles' => null,
    'users' => null,
  ];

  /**
   * Collection of fields maintained by the current instance.
   *
   * @var array<string,BaseField>
   */
  protected $children = null;

  /**
   * Load field localization.
   */
  public static function setup() {

    $base = __DIR__ . DS . 'languages' . DS;
    $lang = panel()->translation()->code();

    if (file_exists($base . $lang . '.php')) {
      require $base . $lang . '.php';
    } else {
      require $base . 'en.php';
    }

  }

  /**
   * Create a new instance of the FirewallField class.
   */
  public function __construct() {

    $this->text = $this->l('label');
    $this->default = true;

  }

  /**
   * Fetch a language variable for multi-language sites. Takes into account the
   * text domain of the current field.
   *
   * @param string $value
   * @return string
   */
  public function l($value) {
    return l('fields.' . self::FIELDNAME . '.' . $value);
  }

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
   * Get the default value to use when no value is given by the user.
   *
   * @return mixed
   */
  public function defaultValue() {
    return ($this->default === '') ? '1' : $this->default;
  }

  /**
   * Synchronize the value of the current field with those of the child
   * elements. We need to assign the value manually as the child elements
   * are detached from the actual form.
   *
   * @param array<string,mixed>? $data Optional data store to synchronize.
   */
  public function sync($data = null) {

    if (is_null($data)) {
      $data = $this->value();
    } else {
      $this->value = $data;
    }

    foreach ($this->fields() as $name => $field) {
      $field->value = a::get($data, $name);
    }

  }

  /**
   * Collect request data for all fields that belong to the current instance.
   *
   * @return array<string,array>
   */
  public function data() {

    $data = [];

    foreach ($this->fields() as $name => $field) {
      $data[$name] = get($field->name());
    }

    return $data;

  }

  /**
   * Get the current value of the field instance.
   *
   * @return mixed
   */
  public function value() {

    if ($this->value === '') {
      $this->value = $this->defaultValue();
    } else if (is_string($this->value) && !v::accepted($this->value) && !v::denied($this->value)) {
      $this->value = yaml::decode($this->value);
    }

    return $this->value;

  }

  /**
   * Validate the selected field values.
   *
   * @return bool
   */
  public function validate() {

    // Validate the value of the checkbox
    $value = get($this->name());

    if (!(is_null($value) || v::accepted($value) || v::denied($value))) {
      return false;
    }

    // Validate the value of all fields attached to the current instance
    $this->sync($this->data());

    foreach ($this->fields() as $name => $field) {
      if (!$field->validate()) {
        return false;
      }
    }

    // All test have passed. Data can be saved to file.
    return true;
  }

  /**
   * Generate the field value that can be saved within the corresponding content
   * file. Converts the current value to a yaml formatted string.
   *
   * @param bool $encode Whether to yaml encode the result.
   * @return string
   */
  public function result($encode = true) {

    $checked = get($this->name());

    // Access control is disabled. Page is public.
    if (v::accepted($checked)) {
      return $encode ? '1' : true;
    }

    // Collect user and role names selected by the user.
    $data = $this->data();
    $data = array_filter($data);

    // Access control is enabled. Page is hidden for all.
    if (empty($data)) {
      return $encode ? '0' : false;
    }

    // Access control is enabled. Page is visile for specific users/roles only.
    return $encode ? yaml::encode($data) : $result;

  }

  /**
   * Test whether the user has enabled access control for the current page.
   *
   * @return bool
   */
  public function checked() {

    $value = $this->value();
    return !is_array($value) && v::accepted($value);

  }

  /**
   * Get the markup for the input element of the field.
   *
   * @return \Brick
   */
  public function input() {

    $container = brick('label')
      ->text($this->i18n($this->text()))
      ->attr('for', $this->id())
      ->addClass('input')
      ->addClass('input-with-checkbox');

    $input = brick('input')
      ->addClass('checkbox')
      ->addClass('js-toggle-panel')
      ->attr([
        'type'          => 'checkbox',
        'id'            => $this->id(),
        'name'          => $this->name(),
        'required'      => $this->required(),
        'checked'       => $this->checked(),
        'readonly'      => $this->readonly(),
        'autofocus'     => $this->autofocus(),
        'autocomplete'  => $this->autocomplete(),
        'aria-controls' => $this->id() . '-panel',
        'aria-expanded' => $this->checked() ? 'false' : 'true',
      ]);

    if($this->readonly()) {
      $input->attr('tabindex', '-1');
      $container->addClass('input-is-readonly');
    }

    return $container->prepend($input);

  }

  /**
   * Generate the markup for the field container.
   *
   * @see https://forum.getkirby.com/t/panel-field-javascript-click-does-not-work-after-save/3474/7 Panel field javascript click does not work after save
   * @return \Brick
   */
  public function element() {

    return parent::element()
      ->data('field', self::FIELDNAME . 'field')
      ->addClass('acf');

  }

  /**
   * Get all fields that belong to the current field instance. If the `fields`
   * paramater is given new fields are added to the footer of the field.
   *
   * @return array<string,BaseField>
   */
  public function fields() {

    if (!is_null($this->children)) {
      return $this->children;
    }

    $fields = [];
    $state  = $this->value();
    $user   = panel()->user();

    $defaults = [
      'page'        => $this->page,
      'model'       => $this->model,
      'parentField' => $this,
      'columns'     => $this->columns(),
    ];

    foreach (self::$fields as $name) {

      // Ensure the field exists and the user has access to the corresponding
      // content type
      $classname = ucfirst($name) . 'Field';

      if (!class_exists($classname) || !$user->can("panel.{$name}.read")) {
        continue;
      }

      // Create a new field instance with sensitive defaults
      $instance = new $classname;

      $options = array_merge($defaults, [
        'name'    => $name,
        'label'   => $this->l($name),
        'value'   => a::get($state, $name),
        'exclude' => a::get($this->exclude, $name, []),
      ]);

      foreach ($options as $key => $value) {
        if (property_exists($instance, $key)) {
          $instance->{$key} = $value;
        }
      }

      // Add the new field to the registry
      $fields[$name] = $instance;

    }

    return $this->children = $fields;

  }

  /**
   * Generate the markup for the entire field element with all its input
   * elements and accompanying contents.
   *
   * @return \Brick
   */
  public function template() {

    // Append the markup for all child elements to the container
    $container = brick('div')
      ->addClass('field-panel')
      ->attr([
        'id' => $this->id() . '-panel',
      ]);

    // Ensure that the values of all fields attached to the current instance
    // are synchronized with the root
    $this->sync();

    // Generate the markup of each field attached
    foreach ($this->fields() as $name => $field) {
      $type = str_replace('field', '', strtolower(get_class($field)));
      $child = $field->template();
      $child
        ->removeClass('field-grid-item')
        ->addClass('acf-bubble')
        ->addClass('acf-bubble-up')
        ->addClass('js-acf-' . str::slug($type));
      $container->append($child);
    }

    // Generate the markup for the select field and append the child elements.
    return parent::template()->append($container);

  }

}
