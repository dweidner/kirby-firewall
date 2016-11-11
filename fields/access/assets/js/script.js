/*! AccessField - Daniel Weidner <hallo@danielweidner.de> */

var AccessFieldPanel = (function($, $field) {

  "use strict";

  /**
   * A reference to the current function scope. Can be used within callback
   * functions.
   *
   * @type {Function}
   */
  var self = this;

  /**
   * Element that toggles the visibility of the panel.
   *
   * @type {jQuery}
   */
  self.$toggle = $field.find('.js-toggle-panel');

  /**
   * Panel element containing additional fields.
   *
   * @type {jQuery}
   */
  self.$panel = $field.find('#' + self.$toggle.attr('aria-controls'));

  /**
   * Initialize the field instance.
   *
   * @returns {self}
   */
  self.init = function () {

    self.$panel.css('max-height', self.$panel.outerHeight() + 16);

    if (self.isExpanded()) {
      self.expand();
    } else {
      self.collapse();
    }

    return self.bind();
  };

  /**
   * Check whether the panel is currently expanded.
   *
   * @return {Boolean}
   */
  self.isExpanded = function() {
    return self.$toggle.attr('aria-expanded') === 'true';
  };

  /**
  * Registers event handlers.
  *
    * @returns {self}
  */
  self.bind = function() {

    self.$toggle.on('change', $.proxy(self.toggle, self));

    return self;

  };

  /**
   * Expand the panel element.
   */
  self.expand = function() {

    self.$toggle.attr('aria-expanded', true);
    self.$panel.attr('aria-hidden', false);

    return self;

  };

  /**
   * Collapse the panel element.
   */
  self.collapse = function() {

    self.$toggle.attr('aria-expanded', false);
    self.$panel.attr('aria-hidden', true);

    return self;

  };

  /**
   * Toggle the visibility of the panel element.
   */
  self.toggle = function() {
    return self.isExpanded() ? self.collapse() : self.expand();
  };

  return self.init();

});

(function($) {

  /**
   * Create a new instance of the AccessField.
   *
   * @returns {AccessField}
   */
  $.fn.accessfield = function() {

    var panel = this.data('access-field-panel');

    if (!panel) {
      panel = new AccessFieldPanel($, this);
      this.data('access-field-panel', panel);
    }

    return panel;

  };

 })(jQuery);
