(function ($, Drupal, drupalSettings) {
  var inputBox = null;

  Drupal.behaviors.commercePosKeypadKeypad = {
    attach: function (context, settings) {
      if (drupalSettings.commerce_pos_keypad.commerce_pos_keypad.commercePosKeypadKeypad && drupalSettings.commerce_pos_keypad.commerce_pos_keypad.commercePosKeypadKeypad.inputBox) {
        var counter = 0;
        // TODO this only supports 1 instance of inputBox properly, if we want to do different types on the same page it won't work right.
        $('.commerce-pos-keypad-keypad').once('commerce-pos-keypad-keypad').each(function () {
          var uniqueClass = 'commerce-pos-keypad-instance-' + counter;
          $(this).addClass(uniqueClass);
          inputBox = new KeypadInputBox("." + uniqueClass);
          counter++;
        });
      }

      // TODO this should actually be properly a unique setting per instance
      if (drupalSettings.commerce_pos_keypad.commerce_pos_keypad.commercePosKeypadIcon) {
        $('.commerce-pos-keypad-keypad').each(function () {
          var _this = $(this);
          _this.once('commerce-pos-keypad-keypad-processed').each(function () {
            inputBox.hide();
            _this.after('<div class="commerce-pos-keypad-keypad-icon">&#9000;</div>');

            _this.siblings('.commerce-pos-keypad-keypad-icon').click(function () {
              inputBox.setTextInput(_this);
              inputBox.toggle();
            });
          });
        });
      }
    }
  };

  var KeypadInputBox = function(uniqueIdentifier) {
    this.output = '';
    this.submitEvents = [];
    this.uniqueIdentifier = uniqueIdentifier;
    this.construct();
  };

  KeypadInputBox.prototype.setTextInput = function (element) {
    this.textInput = element;
  };

  KeypadInputBox.prototype.toggle = function toggle() {
    $(this.uniqueIdentifier).parent().find('.commerce-pos-keypad-keys').toggle();
  };

  KeypadInputBox.prototype.show = function show() {
    $(this.uniqueIdentifier).parent().find('.commerce-pos-keypad-keys').show();
  };

  KeypadInputBox.prototype.hide = function hide() {
    $(this.uniqueIdentifier).parent().find('.commerce-pos-keypad-keys').hide();
  };

  KeypadInputBox.prototype.submit = function submit() {
    if (this.submitEvents) {
      this.textInput.val(this.output);

      this.submitEvents.forEach(function (eventInfo) {
        var e = jQuery.Event(eventInfo.name, eventInfo.properties);
        $(eventInfo.selector).trigger(e);
      });

      this.hide();
    }
  };

  KeypadInputBox.prototype.construct = function construct() {
    $(this.uniqueIdentifier).after(drupalSettings.commerce_pos_keypad.commerce_pos_keypad.commercePosKeypadKeypad.inputBox);
    this.inputBox = $(this.uniqueIdentifier).parent();
    // The element to update with the current value of what is entered by the
    // keypad.
    this.valueElement = this.inputBox.find('input');
    var self = this;

    $('.commerce-pos-keypad-close').on('click', function(e) {
      self.hide();
      e.preventDefault();
      return false;
    });

    if (drupalSettings.commerce_pos_keypad.commerce_pos_keypad.commercePosKeypadKeypad.events) {
      drupalSettings.commerce_pos_keypad.commerce_pos_keypad.commercePosKeypadKeypad.events.forEach(function (item) {
        self.submitEvents.push({selector: item.selector, name: item.name, properties: item.properties});
      });
    }

    this.inputBox.find('.commerce-pos-keypad .commerce-pos-keypad-key').each(function(index) {
      $(this).on('click', function() {
        var _this = $(this);

        if (_this.data('keybind') !== undefined) {
          self.output += _this.data('keybind');
        }

        if (_this.data('key-action')) {
          switch (_this.data('key-action')) {
            case 'clear':
              self.output = "";
              break;
            case 'backspace':
              self.output = self.output.slice(0, -1);
              break;
            case 'submit':
              self.submit();
              break;
          }
        }

        self.updateValue();
      });
    });
  };

  KeypadInputBox.prototype.updateValue = function() {
    this.valueElement.val(this.output);
  };

}(jQuery, Drupal, drupalSettings));
