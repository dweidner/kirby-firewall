/*! AccessField - Daniel Weidner <hallo@danielweidner.de> */

var AccessField = (function($, $field) {

  /**
   * A reference to the current function scope. Can be used within callback
   * functions.
   *
   * @type {Function}
   */
  var self = this;

  /**
   * Name of the class name used to identify elements with custom behaviors.
   *
   * @type {string}
   */
  var hook = 'js-acf';

  /**
   * Collection of HTMLElements manipulated by the script.
   *
   * @type {Object<string, jQuery>}
   */
  this.$ = {
    field: $field,
    users: $field.find('.' + hook + '-users'),
    roles: $field.find('.' + hook + '-roles'),
  };

  /**
   * Initialize the field instance.
   *
   * @returns {self}
   */
  this.init = function () {
    self.select(self.type());
    self.bind();
    return self;
  };

  /**
   * Registers event handlers.
   *
   * @returns {self}
   */
  this.bind = function() {
    self.$.field
      .find('select')
      .on('change', function(e) {
        self.select(this.value);
      });

    return self;
  };

  /**
   * Get the currently selected control type.
   *
   * @returns {string}
   */
  this.type = function() {
    return self.$.field.find('select').val();
  };

  /**
   * Select the given control type.
   *
   * @param {string} type Access control type selected by the user.
   * @returns {self}
   */
  this.select = function(type) {
    var className = 'hidden';

    if (type === 'users') {
      self.$.users.removeClass(className);
      self.$.roles.addClass(className);
    } else if (type === 'roles') {
      self.$.users.addClass(className);
      self.$.roles.removeClass(className);
    } else {
      self.$.users.addClass(className);
      self.$.roles.addClass(className);
    }

    return self;
  };

  return this.init();

});

(function($) {

  /**
   * Create a new instance of the AccessField.
   *
   * @returns {AccessField}
   */
  $.fn.accessfield = function() {
    return new AccessField($, this);
  };

 })(jQuery);
