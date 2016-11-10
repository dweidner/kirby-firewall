<?php

/**
 * Access Control Field
 *
 * Custom field that allows to restrict the access to a page to
 * specific users or roles only.
 *
 * @author     Daniel Weidner <hallo@danielweidner.de>
 * @package    Kirby\Plugin\Firewall
 * @subpackage AccessField
 * @since      1.0.0
 */
class AccessField extends SelectField {

  /**
   * Version of the field.
   *
   * @var string
   */
  const VERSION = '1.0.0-beta';

  /**
   * Name of the custom field. Represents the identifier users have to use
   * within their blueprints.
   *
   * @var string
   */
  const FIELDNAME = 'access';

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
   * List of field properties that can not be changed by the user via the
   * blueprint.
   *
   * @var array<string,mixed>
   */
  public static $trumps = [
    'required' => true,
    'default'  => 'public',
  ];

  /**
   * A list of possible entity types the user can select from, with `public` as
   * the default.
   *
   * @var array<string>
   */
  public static $choices = [
    'public',
    'users',
    'roles',
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
    'users' => null,
    'roles' => null
  ];

  /**
   * Collection of fields controled by the current instance.
   *
   * @var array<string,BaseField>
   */
  protected $children = [];

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
   * Create a new instance of the AccessField class.
   */
  public function __construct() {

    parent::__construct();

    foreach (self::$trumps as $key => $value) {
      if (property_exists($this, $key)) {
        $this->{$key} = $value;
      }
    }

    $fields = array_slice(self::$choices, 1);
    $this->children($fields);

  }

  /**
   * Get the name of the current field instance.
   *
   * @return string
   */
  public function name() {

    $prefix = ($this->parentField instanceof BaseField) ? $this->parentField->name() . '-' : '';
    return $prefix . $this->name;

  }

  /**
   * Get the current value of the field instance.
   *
   * @return array<string,mixed>
   */
  public function value() {

    // Expect the field value as yaml formatted string. Provide a default value
    // if none is given yet.
    if (is_null($this->value)) {
      $this->value = [ 'type' => $this->default() ];
    } else if (is_string($this->value)) {
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

    // Retrieve the current field values
    $data = $this->result(false);

    // Synchronize field value between components
    $this->sync($data);

    // Validate all child components
    foreach ($this->children() as $name => $field) {
      if (!$field->validate()) {
        return false;
      }
    }

    // Validate the current field
    return array_key_exists(a::get($data, 'type'), $this->options());

  }

  /**
   * Generate the field value that can be saved within the corresponding content
   * file. Converts the current value to a yaml formatted string.
   *
   * @param bool $encode Whether to yaml encode the result.
   * @return string
   */
  public function result($encode = true) {

    $type = get($this->name());

    if ($this->default() === $type) {
      $result  = compact('type');
    } else {
      $field   = a::get($this->children(), $type);
      $result  = [
        'type' => $type,
        $type  => (array)get($field->name()),
      ];
    }

    return $encode ? yaml::encode($result) : $result;

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

    foreach ($this->children() as $name => $field) {
      $field->value = a::get($data, $name);
    }

  }

  /**
   * Fetch a language variable for multi-language sites. Takes into account the
   * text domain of the current field.
   *
   * @param string $value
   * @return string
   */
  public function l($value) {
    return $this->i18n('fields.' . self::FIELDNAME . '.' . $value);
  }

  /**
   * Setup all child elements that belong to the current field.
   *
   * @return array<string,BaseField>
   */
  public function children($fields = null) {

    if (is_null($fields)) {
      return $this->children;
    }

    $user = panel()->user();
    $state = $this->value();

    $defaults = [
      'page'        => $this->page,
      'model'       => $this->model,
      'parentField' => $this,
      'columns'     => $this->columns(),
    ];

    foreach ($fields as $name) {

      $classname = ucfirst($name) . 'Field';

      if (!class_exists($classname) || !$user->can("panel.{$name}.read")) {
        continue;
      }

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

      $this->children[$name] = $instance;
    }

    return $this->children;

  }

  /**
   * Generate an option element for each predefined choice.
   *
   * @return array<string,string>
   */
  public function options() {

    $user = panel()->user();
    $options  = [];

    foreach (self::$choices as $choice) {
      if (($this->default() === $choice) || $user->can("panel." . $choice . ".read")) {
        $options[$choice] = $this->l('type.' . $choice);
      }
    }

    return $options;

  }

  /**
   * Generate the markup for a select option.
   *
   * @param mixed $value Value of the option.
   * @param mixed $text Text displayed for the option.
   * @param mixed $selected Whether the option is the currently selected one.
   */
  public function option($value, $text, $selected = false) {

    return brick('option', $this->i18n($text), [
      'value'    => $value,
      'selected' => $selected || ($value === a::get($this->value(), 'type')),
    ]);

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
   * Generate the markup for the entire field element with all its input
   * elements and accompanying contents.
   *
   * @return \Brick
   */
  public function template() {

    // Append the markup for all child elements to the container
    $container = brick('div')->addClass('field-aside');

    // Ensure that the values of all components are synchronized
    $this->sync();

    // Generate the markup of each sub-component
    foreach ($this->children() as $name => $field) {
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

  /**
   * Method overloading. `_call`is triggered whenever an inaccesible method
   * is invoked.
   *
   * @param mixed $name
   * @param mixed $args
   */
  public function __call($name, $args) {

    // Enforce the overrides specified in `self::$trumps`
    if (array_key_exists($name, self::$trumps)) {
      return self::$trumps[$name];
    }

    return isset($this->{$name}) ? $this->{$name} : null;

  }

}
