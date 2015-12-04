/*!
 * Validator (http://bootstrapvalidator.com)
 * The best jQuery plugin to validate form fields. Designed to use with Bootstrap 3
 *
 * @version     v0.5.3, built on 2015-05-21 4:54:41 PM
 * @author      https://twitter.com/nghuuphuoc
 * @copyright   (c) 2013 - 2015 Nguyen Huu Phuoc
 * @license     MIT
 */
(function($) {

var Validator = function(form, options) {
    this.$form   = $(form);
    this.options = $.extend({}, $.fn.Validator.DEFAULT_OPTIONS, options);

    this.$invalidFields = $([]);    // Array of invalid fields
    this.$submitButton  = null;     // The submit button which is clicked to submit form
    this.$hiddenButton  = null;

    // Validating status
    this.STATUS_NOT_VALIDATED = 'NOT_VALIDATED';
    this.STATUS_VALIDATING    = 'VALIDATING';
    this.STATUS_INVALID       = 'INVALID';
    this.STATUS_VALID         = 'VALID';

    // Determine the event that is fired when user change the field value
    // Most modern browsers supports input event except IE 7, 8.
    // IE 9 supports input event but the event is still not fired if I press the backspace key.
    // Get IE version
    // https://gist.github.com/padolsey/527683/#comment-7595
    var ieVersion = (function() {
        var v = 3, div = document.createElement('div'), a = div.all || [];
        while (div.innerHTML = '<!--[if gt IE '+(++v)+']><br><![endif]-->', a[0]) {}
        return v > 4 ? v : !v;
    }());

    var el = document.createElement('input');
    this._changeEvent = (ieVersion === 9 || !('oninput' in el)) ? 'keyup' : 'input';

    // The flag to indicate that the form is ready to submit when a remote/callback validator returns
    this._submitIfValid = null;

    // Field elements
    this._cacheFields = {};

    this._init();
};

Validator.prototype = {
    constructor: Validator,

    /**
     * Init form
     */
    _init: function() {
        var that    = this,
            options = {
                autoFocus:      this.$form.attr('data-validate-autofocus'),
                disptype:       this.$form.attr('data-validate-disptype'),
                container:      this.$form.attr('data-validate-container'),
                events: {
                    formInit:         this.$form.attr('data-validate-events-form-init'),
                    formError:        this.$form.attr('data-validate-events-form-error'),
                    formSuccess:      this.$form.attr('data-validate-events-form-success'),
                    fieldAdded:       this.$form.attr('data-validate-events-field-added'),
                    fieldRemoved:     this.$form.attr('data-validate-events-field-removed'),
                    fieldInit:        this.$form.attr('data-validate-events-field-init'),
                    fieldError:       this.$form.attr('data-validate-events-field-error'),
                    fieldSuccess:     this.$form.attr('data-validate-events-field-success'),
                    fieldStatus:      this.$form.attr('data-validate-events-field-status'),
                    validatorError:   this.$form.attr('data-validate-events-validator-error'),
                    validatorSuccess: this.$form.attr('data-validate-events-validator-success')
                },
                excluded:       this.$form.attr('data-validate-excluded'),
                icons: {
                    valid:      this.$form.attr('data-validate-icon-valid'),
                    invalid:    this.$form.attr('data-validate-icon-invalid'),
                    validating: this.$form.attr('data-validate-icon-validating')
                },
                group:          this.$form.attr('data-validate-group'),
                live:           this.$form.attr('data-validate-live'),
                message:        this.$form.attr('data-validate-message'),
                onError:        this.$form.attr('data-validate-onerror'),
                onSuccess:      this.$form.attr('data-validate-onsuccess'),
                submitButtons:  this.$form.attr('data-validate-submitbuttons'),
                threshold:      this.$form.attr('data-validate-threshold'),
                trigger:        this.$form.attr('data-validate-trigger'),
                verbose:        this.$form.attr('data-validate-verbose'),
                fields:         {}
            };

        this.$form
            // Disable client side validation in HTML 5
            .attr('novalidate', 'novalidate')
            .addClass(this.options.elementClass)
            // Disable the default submission first
            .on('submit.validate', function(e) {
                e.preventDefault();
                that.validate();
            })
            .on('click.validate', this.options.submitButtons, function() {
                that.$submitButton  = $(this);
                // The user just click the submit button
                that._submitIfValid = true;
            })
            // Find all fields which have either "name" or "data-validate-field" attribute
            .find('[name], [data-validate-field]')
                .each(function() {
                    var $field = $(this),
                        field  = $field.attr('name') || $field.attr('data-validate-field'),
                        opts   = that._parseOptions($field);
                    if (opts) {
                        $field.attr('data-validate-field', field);
                        options.fields[field] = $.extend({}, opts, options.fields[field]);
                    }
                });

        this.options = $.extend(true, this.options, options);

        // When pressing Enter on any field in the form, the first submit button will do its job.
        // The form then will be submitted.
        // I create a first hidden submit button
        this.$hiddenButton = $('<button/>')
                                .attr('type', 'submit')
                                .prependTo(this.$form)
                                .addClass('hidden-submit')
                                .css({ display: 'none', width: 0, height: 0 });

        this.$form
            .on('click.validate', '[type="submit"]', function(e) {
                // #746: Check if the button click handler returns false
                if (!e.isDefaultPrevented()) {
                    var $target = $(e.target),
                        // The button might contain HTML tag
                        $button = $target.is('[type="submit"]') ? $target.eq(0) : $target.parent('[type="submit"]').eq(0);

                    // Don't perform validation when clicking on the submit button/input
                    // which aren't defined by the 'submitButtons' option
                    if (that.options.submitButtons && !$button.is(that.options.submitButtons) && !$button.is(that.$hiddenButton)) {
                        that.$form.off('submit.validate').submit();
                    }
                }
            });

        for (var field in this.options.fields) {
            this._initField(field);
        }

        this.$form.trigger($.Event(this.options.events.formInit), {
            validate: this,
            options: this.options
        });

        // Prepare the events
        if (this.options.onSuccess) {
            this.$form.on(this.options.events.formSuccess, function(e) {
                $.fn.Validator.helpers.call(that.options.onSuccess, [e]);
            });
        }
        if (this.options.onError) {
            this.$form.on(this.options.events.formError, function(e) {
                $.fn.Validator.helpers.call(that.options.onError, [e]);
            });
        }
    },

    /**
     * Parse the validator options from HTML attributes
     *
     * @param {jQuery} $field The field element
     * @returns {Object}
     */
    _parseOptions: function($field) {
        var field      = $field.attr('name') || $field.attr('data-validate-field'),
            validators = {},
            validator,
            v,          // Validator name
            vi,
            attrName,
            enabled,
            optionName,
            optionAttrName,
            optionValue,
            html5AttrName,
            html5AttrMap;

        for (v in $.fn.Validator.validators) {
            validator    = $.fn.Validator.validators[v];
            attrName     = 'data-validate-' + v.toLowerCase(),
            enabled      = $field.attr(attrName) + '';
            html5AttrMap = ('function' === typeof validator.enableByHtml5) ? validator.enableByHtml5($field) : null;

            if(typeof validator.html5Attributes === 'object') {
                for(var vi in validator.html5Attributes) {
                    if($field.is('[data-validate-'+ v.toLowerCase() + '-'+ vi +']')) {
                        enabled = 'true';
                        break;
                    }
                }
            }

            if ((html5AttrMap && enabled !== 'false')
                || (html5AttrMap !== true && ('' === enabled || 'true' === enabled || attrName === enabled.toLowerCase())))
            {
                // Try to parse the options via attributes
                validator.html5Attributes = $.extend({}, { message: 'message', onerror: 'onError', onsuccess: 'onSuccess' }, validator.html5Attributes);
                validators[v] = $.extend({}, html5AttrMap === true ? {} : html5AttrMap, validators[v]);

                for (html5AttrName in validator.html5Attributes) {
                    optionName  = validator.html5Attributes[html5AttrName];
                    optionAttrName = 'data-validate-' + v.toLowerCase() + '-' + html5AttrName,
                    optionValue = $field.attr(optionAttrName);
                    if (optionValue) {
                        if ('true' === optionValue || optionAttrName === optionValue.toLowerCase()) {
                            optionValue = true;
                        } else if ('false' === optionValue) {
                            optionValue = false;
                        }
                        validators[v][optionName] = optionValue;
                    }
                }
            }
        }

        var opts = {
                autoFocus:     $field.attr('data-validate-autofocus'),
                disptype:      $field.attr('data-validate-disptype'),
                container:     $field.attr('data-validate-container'),
                excluded:      $field.attr('data-validate-excluded'),
                icons:         $field.attr('data-validate-icons'),
                container:     $field.attr('data-validate-container'),
                group:         $field.attr('data-validate-group'),
                message:       $field.attr('data-validate-message'),
                onError:       $field.attr('data-validate-onerror'),
                onStatus:      $field.attr('data-validate-onstatus'),
                onSuccess:     $field.attr('data-validate-onsuccess'),
                selector:      $field.attr('data-validate-selector'),
                threshold:     $field.attr('data-validate-threshold'),
                trigger:       $field.attr('data-validate-trigger'),
                verbose:       $field.attr('data-validate-verbose'),
                validators:    validators
            },
            emptyOptions    = $.isEmptyObject(opts),        // Check if the field options are set using HTML attributes
            emptyValidators = $.isEmptyObject(validators);  // Check if the field validators are set using HTML attributes

        if (!emptyValidators || (!emptyOptions && this.options.fields && this.options.fields[field])) {
            opts.validators = validators;
            return opts;
        } else {
            return null;
        }
    },

    /**
     * Init field
     *
     * @param {String|jQuery} field The field name or field element
     */
    _initField: function(field) {
        var fields = $([]);
        switch (typeof field) {
            case 'object':
                fields = field;
                field  = field.attr('data-validate-field');
                break;
            case 'string':
                fields = this.getFieldElements(field);
                fields.attr('data-validate-field', field);
                break;
            default:
                break;
        }

        // We don't need to validate non-existing fields
        if (fields.length === 0) {
            return;
        }

        if (this.options.fields[field] === null || this.options.fields[field].validators === null) {
            return;
        }

        var validatorName;
        for (validatorName in this.options.fields[field].validators) {
            if (!$.fn.Validator.validators[validatorName]) {
                delete this.options.fields[field].validators[validatorName];
            }
        }
        if (this.options.fields[field].enabled === null) {
            this.options.fields[field].enabled = true;
        }

        var that      = this,
            total     = fields.length,
            type      = fields.attr('type'),
            updateAll = (total === 1) || ('radio' === type) || ('checkbox' === type),
            disptype,
            event     = ('radio' === type || 'checkbox' === type || 'file' === type || 'SELECT' === fields.eq(0).get(0).tagName) ? 'change' : this._changeEvent,
            trigger   = (this.options.fields[field].trigger || this.options.trigger || event).split(' '),
            events    = $.map(trigger, function(item) {
                return item + '.update.validate';
            }).join(' ');

        for (var i = 0; i < total; i++) {
            var $field    = fields.eq(i),
                group     = this.options.fields[field].group || this.options.group,
                $parent   = $field.parents(group),
                // Allow user to indicate where the error messages are shown
                container = ('function' === typeof (this.options.fields[field].container || this.options.container)) ? (this.options.fields[field].container || this.options.container).call(this, $field, this) : (this.options.fields[field].container || this.options.container),
                $message  = (container && container !== 'tooltip' && container !== 'popover') ? $(container) : this._getMessageContainer($field, group);

            if (container && container !== 'tooltip' && container !== 'popover') {
                $message.addClass('has-error');
            }

            // Remove all error messages and feedback icons
            $message.find('[class|=help][data-validate-validator][data-validate-for="' + field + '"]').remove();
            $parent.find('i[data-validate-icon-for="' + field + '"]').remove();

            // Whenever the user change the field value, mark it as not validated yet
            $field.off(events).on(events, function() {
                that.updateStatus($(this), that.STATUS_NOT_VALIDATED);
            });

            // Create help block or inline elements for showing the error messages
            $field.data('validate.messages', $message);
            for (validatorName in this.options.fields[field].validators) {
                $field.data('validate.result.' + validatorName, this.STATUS_NOT_VALIDATED);
                if(this.options.disptype === 'inline' || this.options.fields[field].disptype === 'inline') {
                    disptype = 'inline';
                }
                if(this.options.disptype === 'inline-block' || this.options.fields[field].disptype === 'inline-block') {
                    disptype = 'inline-block';
                }
                else {
                    disptype = 'block';
                }

                if (!updateAll || i === total - 1) {
                    $('<ins/>')
                        .css('display', 'none')
                        .addClass('help-' + disptype)
                        .attr('data-validate-validator', validatorName)
                        .attr('data-validate-for', field)
                        .attr('data-validate-result', this.STATUS_NOT_VALIDATED)
                        .html(this._getMessage(field, validatorName))
                        .appendTo($message);
                }

                // Init the validator
                if ('function' === typeof $.fn.Validator.validators[validatorName].init) {
                    $.fn.Validator.validators[validatorName].init(this, $field, this.options.fields[field].validators[validatorName]);
                }
            }

            // Prepare the feedback icons
            // Available from Bootstrap 3.1 (http://getbootstrap.com/css/#forms-control-validation)
            if (this.options.fields[field].icons !== false && this.options.fields[field].icons !== 'false'
                && this.options.icons
                && this.options.icons.validating && this.options.icons.invalid && this.options.icons.valid
                && (!updateAll || i === total - 1))
            {
                // $parent.removeClass('has-success').removeClass('has-error').addClass('has-feedback');
                // Keep error messages which are populated from back-end
                $parent.addClass('has-feedback');
                var $icon = $('<i/>')
                                .css('display', 'none')
                                .addClass('form-control-feedback')
                                .attr('data-validate-icon-for', field)
                                .insertAfter($field);

                // Place it after the container of checkbox/radio
                // so when clicking the icon, it doesn't effect to the checkbox/radio element
                if ('checkbox' === type || 'radio' === type) {
                    var $fieldParent = $field.parent();
                    if ($fieldParent.hasClass(type)) {
                        $icon.insertAfter($fieldParent);
                    } else if ($fieldParent.parent().hasClass(type)) {
                        $icon.insertAfter($fieldParent.parent());
                    }
                }

                // The feedback icon does not render correctly if there is no label
                // https://github.com/twbs/bootstrap/issues/12873
                if ($parent.find('label').length === 0) {
                    $icon.addClass('validate-no-label');
                }
                // Fix feedback icons in input-group
                if ($parent.find('.input-group').length !== 0) {
                    $icon.addClass('validate-icon-input-group')
                        .insertAfter($parent.find('.input-group').eq(0));
                }

                // Store the icon as a data of field element
                if (!updateAll) {
                    $field.data('validate.icon', $icon);
                } else if (i === total - 1) {
                    // All fields with the same name have the same icon
                    fields.data('validate.icon', $icon);
                }

                if (container) {
                    $field
                        // Show tooltip/popover message when field gets focus
                        .off('focus.container.validate')
                        .on('focus.container.validate', function() {
                            switch (container) {
                                case 'tooltip':
                                    $(this).data('validate.icon').tooltip('show');
                                    break;
                                case 'popover':
                                    $(this).data('validate.icon').popover('show');
                                    break;
                                default:
                                    break;
                            }
                        })
                        // and hide them when losing focus
                        .off('blur.container.validate')
                        .on('blur.container.validate', function() {
                            switch (container) {
                                case 'tooltip':
                                    $(this).data('validate.icon').tooltip('show');
                                    break;
                                case 'popover':
                                    $(this).data('validate.icon').popover('show');
                                    break;
                                default:
                                    break;
                            }
                        });
                }
            }
        }

        // Prepare the events
        fields
            .on(this.options.events.fieldSuccess, function(e, data) {
                var onSuccess = that.getOptions(data.field, null, 'onSuccess');
                if (onSuccess) {
                    $.fn.Validator.helpers.call(onSuccess, [e, data]);
                }
            })
            .on(this.options.events.fieldError, function(e, data) {
                var onError = that.getOptions(data.field, null, 'onError');
                if (onError) {
                    $.fn.Validator.helpers.call(onError, [e, data]);
                }
            })
            .on(this.options.events.fieldStatus, function(e, data) {
                var onStatus = that.getOptions(data.field, null, 'onStatus');
                if (onStatus) {
                    $.fn.Validator.helpers.call(onStatus, [e, data]);
                }
            })
            .on(this.options.events.validatorError, function(e, data) {
                var onError = that.getOptions(data.field, data.validator, 'onError');
                if (onError) {
                    $.fn.Validator.helpers.call(onError, [e, data]);
                }
            })
            .on(this.options.events.validatorSuccess, function(e, data) {
                var onSuccess = that.getOptions(data.field, data.validator, 'onSuccess');
                if (onSuccess) {
                    $.fn.Validator.helpers.call(onSuccess, [e, data]);
                }
            });

        // Set live mode
        events = $.map(trigger, function(item) {
            return item + '.live.validate';
        }).join(' ');
        switch (this.options.live) {
            case 'submitted':
                break;
            case 'disabled':
                fields.off(events);
                break;
            case 'enabled':
            /* falls through */
            default:
                fields.off(events).on(events, function() {
                    if (that._exceedThreshold($(this))) {
                        that.validateField($(this));
                    }
                });
                break;
        }

        fields.trigger($.Event(this.options.events.fieldInit), {
            validate: this,
            field: field,
            element: fields
        });
    },

    /**
     * Get the error message for given field and validator
     *
     * @param {String} field The field name
     * @param {String} validatorName The validator name
     * @returns {String}
     */
    _getMessage: function(field, validatorName) {
        if (!this.options.fields[field] || !$.fn.Validator.validators[validatorName]
            || !this.options.fields[field].validators || !this.options.fields[field].validators[validatorName])
        {
            return '';
        }

        var options = this.options.fields[field].validators[validatorName];
        switch (true) {
            case (!!options.message):
                return options.message;
            case (!!this.options.fields[field].message):
                return this.options.fields[field].message;
            case (!!$.fn.Validator.i18n[validatorName]):
                return $.fn.Validator.i18n[validatorName]['default'];
            default:
                return this.options.message;
        }
    },

    /**
     * Get the element to place the error messages
     *
     * @param {jQuery} $field The field element
     * @param {String} group
     * @returns {jQuery}
     */
    _getMessageContainer: function($field, group) {
        var $parent = $field.parent();
        if ($parent.is(group)) {
            return $parent;
        }

        var cssClasses = $parent.attr('class');
        if (!cssClasses) {
            return this._getMessageContainer($parent, group);
        }

        cssClasses = cssClasses.split(' ');
        var n = cssClasses.length;
        for (var i = 0; i < n; i++) {
            if (/^col-(xs|sm|md|lg)-\d+$/.test(cssClasses[i]) || /^col-(xs|sm|md|lg)-offset-\d+$/.test(cssClasses[i])) {
                return $parent;
            }
        }

        return this._getMessageContainer($parent, group);
    },

    /**
     * Called when all validations are completed
     */
    _submit: function() {
        var isValid   = this.isValid(),
            eventType = isValid ? this.options.events.formSuccess : this.options.events.formError,
            e         = $.Event(eventType);

        this.$form.trigger(e);

        // Call default handler
        // Check if whether the submit button is clicked
        if (this.$submitButton) {
            isValid ? this._onSuccess(e) : this._onError(e);
        }
    },

    /**
     * Check if the field is excluded.
     * Returning true means that the field will not be validated
     *
     * @param {jQuery} $field The field element
     * @returns {Boolean}
     */
    _isExcluded: function($field) {
        var excludedAttr = $field.attr('data-validate-excluded'),
            // I still need to check the 'name' attribute while initializing the field
            field        = $field.attr('data-validate-field') || $field.attr('name');

        switch (true) {
            case (!!field && this.options.fields && this.options.fields[field] && (this.options.fields[field].excluded === 'true' || this.options.fields[field].excluded === true)):
            case (excludedAttr === 'true'):
            case (excludedAttr === ''):
                return true;

            case (!!field && this.options.fields && this.options.fields[field] && (this.options.fields[field].excluded === 'false' || this.options.fields[field].excluded === false)):
            case (excludedAttr === 'false'):
                return false;

            default:
                if (this.options.excluded) {
                    // Convert to array first
                    if ('string' === typeof this.options.excluded) {
                        this.options.excluded = $.map(this.options.excluded.split(','), function(item) {
                            // Trim the spaces
                            return $.trim(item);
                        });
                    }

                    var length = this.options.excluded.length;
                    for (var i = 0; i < length; i++) {
                        if (('string' === typeof this.options.excluded[i] && $field.is(this.options.excluded[i]))
                            || ('function' === typeof this.options.excluded[i] && this.options.excluded[i].call(this, $field, this) === true))
                        {
                            return true;
                        }
                    }
                }
                return false;
        }
    },

    /**
     * Check if the number of characters of field value exceed the threshold or not
     *
     * @param {jQuery} $field The field element
     * @returns {Boolean}
     */
    _exceedThreshold: function($field) {
        var field     = $field.attr('data-validate-field'),
            threshold = this.options.fields[field].threshold || this.options.threshold;
        if (!threshold) {
            return true;
        }
        var cannotType = $.inArray($field.attr('type'), ['button', 'checkbox', 'file', 'hidden', 'image', 'radio', 'reset', 'submit']) !== -1;
        return (cannotType || $field.val().length >= threshold);
    },

    // ---
    // Events
    // ---

    /**
     * The default handler of error.form.validate event.
     * It will be called when there is a invalid field
     *
     * @param {jQuery.Event} e The jQuery event object
     */
    _onError: function(e) {
        if (e.isDefaultPrevented()) {
            return;
        }

        if ('submitted' === this.options.live) {
            // Enable live mode
            this.options.live = 'enabled';
            var that = this;
            for (var field in this.options.fields) {
                (function(f) {
                    var fields  = that.getFieldElements(f);
                    if (fields.length) {
                        var type    = $(fields[0]).attr('type'),
                            event   = ('radio' === type || 'checkbox' === type || 'file' === type || 'SELECT' === $(fields[0]).get(0).tagName) ? 'change' : that._changeEvent,
                            trigger = that.options.fields[field].trigger || that.options.trigger || event,
                            events  = $.map(trigger.split(' '), function(item) {
                                return item + '.live.validate';
                            }).join(' ');

                        fields.off(events).on(events, function() {
                            if (that._exceedThreshold($(this))) {
                                that.validateField($(this));
                            }
                        });
                    }
                })(field);
            }
        }

        // Determined the first invalid field which will be focused on automatically
        for (var i = 0; i < this.$invalidFields.length; i++) {
            var $field    = this.$invalidFields.eq(i),
                autoFocus = this._isOptionEnabled($field.attr('data-validate-field'), 'autoFocus');
            if (autoFocus) {
                // Activate the tab containing the field if exists
                var $tabPane = $field.parents('.tab-pane'), tabId;
                if ($tabPane && (tabId = $tabPane.attr('id'))) {
                    $('a[href="#' + tabId + '"][data-toggle="tab"]').tab('show');
                }

                // Focus the field
                $field.focus();
                break;
            }
        }
    },

    /**
     * The default handler of success.form.validate event.
     * It will be called when all the fields are valid
     *
     * @param {jQuery.Event} e The jQuery event object
     */
    _onSuccess: function(e) {
        if (e.isDefaultPrevented()) {
            return;
        }

        // Submit the form
        this.defaultSubmit();
    },

    /**
     * Called after validating a field element
     *
     * @param {jQuery} $field The field element
     * @param {String} [validatorName] The validator name
     */
    _onFieldValidated: function($field, validatorName) {
        var field         = $field.attr('data-validate-field'),
            validators    = this.options.fields[field].validators,
            counter       = {},
            numValidators = 0,
            data          = {
                validate: this,
                field: field,
                element: $field,
                validator: validatorName,
                result: $field.data('validate.response.' + validatorName)
            };

        // Trigger an event after given validator completes
        if (validatorName) {
            switch ($field.data('validate.result.' + validatorName)) {
                case this.STATUS_INVALID:
                    $field.trigger($.Event(this.options.events.validatorError), data);
                    break;
                case this.STATUS_VALID:
                    $field.trigger($.Event(this.options.events.validatorSuccess), data);
                    break;
                default:
                    break;
            }
        }

        counter[this.STATUS_NOT_VALIDATED] = 0;
        counter[this.STATUS_VALIDATING]    = 0;
        counter[this.STATUS_INVALID]       = 0;
        counter[this.STATUS_VALID]         = 0;

        for (var v in validators) {
            if (validators[v].enabled === false) {
                continue;
            }

            numValidators++;
            var result = $field.data('validate.result.' + v);
            if (result) {
                counter[result]++;
            }
        }

        if (counter[this.STATUS_VALID] === numValidators) {
            // Remove from the list of invalid fields
            this.$invalidFields = this.$invalidFields.not($field);

            $field.trigger($.Event(this.options.events.fieldSuccess), data);
        }
        // If all validators are completed and there is at least one validator which doesn't pass
        else if ((counter[this.STATUS_NOT_VALIDATED] === 0 || !this._isOptionEnabled(field, 'verbose')) && counter[this.STATUS_VALIDATING] === 0 && counter[this.STATUS_INVALID] > 0) {
            // Add to the list of invalid fields
            this.$invalidFields = this.$invalidFields.add($field);

            $field.trigger($.Event(this.options.events.fieldError), data);
        }
    },

    /**
     * Check whether or not a field option is enabled
     *
     * @param {String} option The option name, "verbose", "autoFocus", for example
     * @returns {Boolean}
     */
    _isOptionEnabled: function(field, option) {
        if (this.options.fields[field] && (this.options.fields[field][option] === 'true' || this.options.fields[field][option] === true)) {
            return true;
        }
        if (this.options.fields[field] && (this.options.fields[field][option] === 'false' || this.options.fields[field][option] === false)) {
            return false;
        }
        return this.options[option] === 'true' || this.options[option] === true;
    },

    // ---
    // Public methods
    // ---

    /**
     * Retrieve the field elements by given name
     *
     * @param {String} field The field name
     * @returns {null|jQuery[]}
     */
    getFieldElements: function(field) {
        if (!this._cacheFields[field]) {
            this._cacheFields[field] = (this.options.fields[field] && this.options.fields[field].selector)
                                     ? $(this.options.fields[field].selector)
                                     : this.$form.find('[name="' + field + '"]');
        }

        return this._cacheFields[field];
    },

    /**
     * Get the field options
     *
     * @param {String|jQuery} [field] The field name or field element. If it is not set, the method returns the form options
     * @param {String} [validator] The name of validator. It null, the method returns form options
     * @param {String} [option] The option name
     * @return {String|Object}
     */
    getOptions: function(field, validator, option) {
        if (!field) {
            return option ? this.options[option] : this.options;
        }
        if ('object' === typeof field) {
            field = field.attr('data-validate-field');
        }
        if (!this.options.fields[field]) {
            return null;
        }

        var options = this.options.fields[field];
        if (!validator) {
            return option ? options[option] : options;
        }
        if (!options.validators || !options.validators[validator]) {
            return null;
        }

        return option ? options.validators[validator][option] : options.validators[validator];
    },

    /**
     * Disable/enable submit buttons
     *
     * @param {Boolean} disabled Can be true or false
     * @returns {Validator}
     */
    disableSubmitButtons: function(disabled) {
        if (!disabled) {
            this.$form.find(this.options.submitButtons).removeAttr('disabled');
        } else if (this.options.live !== 'disabled') {
            // Don't disable if the live validating mode is disabled
            this.$form.find(this.options.submitButtons).attr('disabled', 'disabled');
        }

        return this;
    },

    /**
     * Validate the form
     *
     * @returns {Validator}
     */
    validate: function() {
        if (!this.options.fields) {
            return this;
        }
        // this.disableSubmitButtons(true);

        this._submitIfValid = false;
        for (var field in this.options.fields) {
            this.validateField(field);
        }

        this._submit();
        this._submitIfValid = true;

        return this;
    },

    /**
     * Validate given field
     *
     * @param {String|jQuery} field The field name or field element
     * @returns {Validator}
     */
    validateField: function(field) {
        var fields = $([]);
        switch (typeof field) {
            case 'object':
                fields = field;
                field  = field.attr('data-validate-field');
                break;
            case 'string':
                fields = this.getFieldElements(field);
                break;
            default:
                break;
        }

        if (fields.length === 0 || !this.options.fields[field] || this.options.fields[field].enabled === false) {
            return this;
        }

        var that       = this,
            type       = fields.attr('type'),
            total      = ('radio' === type || 'checkbox' === type) ? 1 : fields.length,
            updateAll  = ('radio' === type || 'checkbox' === type),
            validators = this.options.fields[field].validators,
            verbose    = this._isOptionEnabled(field, 'verbose'),
            validatorName,
            validateResult;

        for (var i = 0; i < total; i++) {
            var $field = fields.eq(i);
            if (this._isExcluded($field)) {
                continue;
            }

            var stop = false;
            for (validatorName in validators) {
                if ($field.data('validate.dfs.' + validatorName)) {
                    $field.data('validate.dfs.' + validatorName).reject();
                }
                if (stop) {
                    break;
                }

                // Don't validate field if it is already done
                var result = $field.data('validate.result.' + validatorName);
                if (result === this.STATUS_VALID || result === this.STATUS_INVALID) {
                    this._onFieldValidated($field, validatorName);
                    continue;
                } else if (validators[validatorName].enabled === false) {
                    this.updateStatus(updateAll ? field : $field, this.STATUS_VALID, validatorName);
                    continue;
                }

                $field.data('validate.result.' + validatorName, this.STATUS_VALIDATING);
                validateResult = $.fn.Validator.validators[validatorName].validate(this, $field, validators[validatorName]);

                // validateResult can be a $.Deferred object ...
                if ('object' === typeof validateResult && validateResult.resolve) {
                    this.updateStatus(updateAll ? field : $field, this.STATUS_VALIDATING, validatorName);
                    $field.data('validate.dfs.' + validatorName, validateResult);

                    validateResult.done(function($f, v, response) {
                        // v is validator name
                        $f.removeData('validate.dfs.' + v).data('validate.response.' + v, response);
                        if (response.message) {
                            that.updateMessage($f, v, response.message);
                        }

                        that.updateStatus(updateAll ? $f.attr('data-validate-field') : $f, response.valid ? that.STATUS_VALID : that.STATUS_INVALID, v);

                        if (response.valid && that._submitIfValid === true) {
                            // If a remote validator returns true and the form is ready to submit, then do it
                            that._submit();
                        } else if (!response.valid && !verbose) {
                            stop = true;
                        }
                    });
                }
                // ... or object { valid: true/false, message: 'dynamic message' }
                else if ('object' === typeof validateResult && validateResult.valid !== undefined && validateResult.message !== undefined) {
                    $field.data('validate.response.' + validatorName, validateResult);
                    this.updateMessage(updateAll ? field : $field, validatorName, validateResult.message);
                    this.updateStatus(updateAll ? field : $field, validateResult.valid ? this.STATUS_VALID : this.STATUS_INVALID, validatorName);
                    if (!validateResult.valid && !verbose) {
                        break;
                    }
                }
                // ... or a boolean value
                else if ('boolean' === typeof validateResult) {
                    $field.data('validate.response.' + validatorName, validateResult);
                    this.updateStatus(updateAll ? field : $field, validateResult ? this.STATUS_VALID : this.STATUS_INVALID, validatorName);
                    if (!validateResult && !verbose) {
                        break;
                    }
                }
            }
        }

        return this;
    },

    /**
     * Update the error message
     *
     * @param {String|jQuery} field The field name or field element
     * @param {String} validator The validator name
     * @param {String} message The message
     * @returns {Validator}
     */
    updateMessage: function(field, validator, message) {
        var $fields = $([]);
        switch (typeof field) {
            case 'object':
                $fields = field;
                field   = field.attr('data-validate-field');
                break;
            case 'string':
                $fields = this.getFieldElements(field);
                break;
            default:
                break;
        }

        $fields.each(function() {
            $(this).data('validate.messages').find('.help-block[data-validate-validator="' + validator + '"][data-validate-for="' + field + '"]').html(message);
        });
    },

    /**
     * Update all validating results of field
     *
     * @param {String|jQuery} field The field name or field element
     * @param {String} status The status. Can be 'NOT_VALIDATED', 'VALIDATING', 'INVALID' or 'VALID'
     * @param {String} [validatorName] The validator name. If null, the method updates validity result for all validators
     * @returns {Validator}
     */
    updateStatus: function(field, status, validatorName) {
        var fields = $([]);
        switch (typeof field) {
            case 'object':
                fields = field;
                field  = field.attr('data-validate-field');
                break;
            case 'string':
                fields = this.getFieldElements(field);
                break;
            default:
                break;
        }

        if (status === this.STATUS_NOT_VALIDATED) {
            // To prevent the form from doing submit when a deferred validator returns true while typing
            this._submitIfValid = false;
        }

        var that  = this,
            type  = fields.attr('type'),
            group = this.options.fields[field].group || this.options.group,
            total = ('radio' === type || 'checkbox' === type) ? 1 : fields.length;

        for (var i = 0; i < total; i++) {
            var $field       = fields.eq(i);
            if (this._isExcluded($field)) {
                continue;
            }

            var $parent      = $field.parents(group),
                $message     = $field.data('validate.messages'),
                $allErrors   = $message.find('[class|=help][data-validate-validator][data-validate-for="' + field + '"]'),
                $errors      = validatorName ? $allErrors.filter('[data-validate-validator="' + validatorName + '"]') : $allErrors,
                $icon        = $field.data('validate.icon'),
                container    = ('function' === typeof (this.options.fields[field].container || this.options.container)) ? (this.options.fields[field].container || this.options.container).call(this, $field, this) : (this.options.fields[field].container || this.options.container),
                isValidField = null;

            // Update status
            if (validatorName) {
                $field.data('validate.result.' + validatorName, status);
            } else {
                for (var v in this.options.fields[field].validators) {
                    $field.data('validate.result.' + v, status);
                }
            }

            // Show/hide error elements and feedback icons
            $errors.attr('data-validate-result', status);

            // Determine the tab containing the element
            var $tabPane = $field.parents('.tab-pane'),
                tabId, $tab;
            if ($tabPane && (tabId = $tabPane.attr('id'))) {
                $tab = $('a[href="#' + tabId + '"][data-toggle="tab"]').parent();
            }

            switch (status) {
                case this.STATUS_VALIDATING:
                    isValidField = null;
                    // this.disableSubmitButtons(true);
                    $parent.removeClass('has-success').removeClass('has-error');
                    if ($icon) {
                        $icon.removeClass(this.options.icons.valid).removeClass(this.options.icons.invalid).addClass(this.options.icons.validating).css('display', '');
                    }
                    if ($tab) {
                        $tab.removeClass('validate-tab-success').removeClass('validate-tab-error');
                    }
                    break;

                case this.STATUS_INVALID:
                    isValidField = false;
                    // this.disableSubmitButtons(true);
                    $parent.removeClass('has-success').addClass('has-error');
                    if ($icon) {
                        $icon.removeClass(this.options.icons.valid).removeClass(this.options.icons.validating).addClass(this.options.icons.invalid).css('display', '');
                    }
                    if ($tab) {
                        $tab.removeClass('validate-tab-success').addClass('validate-tab-error');
                    }
                    break;

                case this.STATUS_VALID:
                    // If the field is valid (passes all validators)
                    isValidField = ($allErrors.filter('[data-validate-result="' + this.STATUS_NOT_VALIDATED +'"]').length === 0)
                                 ? ($allErrors.filter('[data-validate-result="' + this.STATUS_VALID +'"]').length === $allErrors.length)  // All validators are completed
                                 : null;                                                                                            // There are some validators that have not done
                    if (isValidField !== null) {
                        // this.disableSubmitButtons(this.$submitButton ? !this.isValid() : !isValidField);
                        if ($icon) {
                            $icon
                                .removeClass(this.options.icons.invalid).removeClass(this.options.icons.validating).removeClass(this.options.icons.valid)
                                .addClass(isValidField ? this.options.icons.valid : this.options.icons.invalid)
                                .css('display', '');
                        }
                    }

                    $parent.removeClass('has-error has-success').addClass(this.isValidContainer($parent) ? 'has-success' : 'has-error');
                    if ($tab) {
                        $tab.removeClass('validate-tab-success').removeClass('validate-tab-error').addClass(this.isValidContainer($tabPane) ? 'validate-tab-success' : 'validate-tab-error');
                    }
                    break;

                case this.STATUS_NOT_VALIDATED:
                /* falls through */
                default:
                    isValidField = null;
                    // this.disableSubmitButtons(false);
                    $parent.removeClass('has-success').removeClass('has-error');
                    if ($icon) {
                        $icon.removeClass(this.options.icons.valid).removeClass(this.options.icons.invalid).removeClass(this.options.icons.validating).hide();
                    }
                    if ($tab) {
                        $tab.removeClass('validate-tab-success').removeClass('validate-tab-error');
                    }
                    break;
            }

            switch (true) {
                // Only show the first error message if it is placed inside a tooltip ...
                case ($icon && 'tooltip' === container):
                    (isValidField === false)
                            ? $icon.css('cursor', 'pointer').tooltip('destroy').tooltip({
                                container: 'body',
                                html: true,
                                placement: 'auto top',
                                title: $allErrors.filter('[data-validate-result="' + that.STATUS_INVALID + '"]').eq(0).html()
                            })
                            : $icon.css('cursor', '').tooltip('destroy');
                    break;
                // ... or popover
                case ($icon && 'popover' === container):
                    (isValidField === false)
                            ? $icon.css('cursor', 'pointer').popover('destroy').popover({
                                container: 'body',
                                content: $allErrors.filter('[data-validate-result="' + that.STATUS_INVALID + '"]').eq(0).html(),
                                html: true,
                                placement: 'auto top',
                                trigger: 'hover click'
                            })
                            : $icon.css('cursor', '').popover('destroy');
                    break;
                default:
                    (status === this.STATUS_INVALID) ? $errors.show() : $errors.hide();
                    break;
            }

            // Trigger an event
            $field.trigger($.Event(this.options.events.fieldStatus), {
                validate: this,
                field: field,
                element: $field,
                status: status
            });
            this._onFieldValidated($field, validatorName);
        }

        return this;
    },

    /**
     * Check the form validity
     *
     * @returns {Boolean}
     */
    isValid: function() {
        for (var field in this.options.fields) {
            if (!this.isValidField(field)) {
                return false;
            }
        }

        return true;
    },

    /**
     * Check if the field is valid or not
     *
     * @param {String|jQuery} field The field name or field element
     * @returns {Boolean}
     */
    isValidField: function(field) {
        var fields = $([]);
        switch (typeof field) {
            case 'object':
                fields = field;
                field  = field.attr('data-validate-field');
                break;
            case 'string':
                fields = this.getFieldElements(field);
                break;
            default:
                break;
        }
        if (fields.length === 0 || !this.options.fields[field] || this.options.fields[field].enabled === false) {
            return true;
        }

        var type  = fields.attr('type'),
            total = ('radio' === type || 'checkbox' === type) ? 1 : fields.length,
            $field, validatorName, status;
        for (var i = 0; i < total; i++) {
            $field = fields.eq(i);
            if (this._isExcluded($field)) {
                continue;
            }

            for (validatorName in this.options.fields[field].validators) {
                if (this.options.fields[field].validators[validatorName].enabled === false) {
                    continue;
                }

                status = $field.data('validate.result.' + validatorName);
                if (status !== this.STATUS_VALID) {
                    return false;
                }
            }
        }

        return true;
    },

    /**
     * Check if all fields inside a given container are valid.
     * It's useful when working with a wizard-like such as tab, collapse
     *
     * @param {String|jQuery} container The container selector or element
     * @returns {Boolean}
     */
    isValidContainer: function(container) {
        var that       = this,
            map        = {},
            $container = ('string' === typeof container) ? $(container) : container;
        if ($container.length === 0) {
            return true;
        }

        $container.find('[data-validate-field]').each(function() {
            var $field = $(this),
                field  = $field.attr('data-validate-field');
            if (!that._isExcluded($field) && !map[field]) {
                map[field] = $field;
            }
        });

        for (var field in map) {
            var $f = map[field];
            if ($f.data('validate.messages')
                  .find('[class|=help][data-validate-validator][data-validate-for="' + field + '"]')
                  .filter('[data-validate-result="' + this.STATUS_INVALID +'"]')
                  .length > 0)
            {
                return false;
            }
        }

        return true;
    },

    /**
     * Submit the form using default submission.
     * It also does not perform any validations when submitting the form
     */
    defaultSubmit: function() {
        if (this.$submitButton) {
            // Create hidden input to send the submit buttons
            $('<input/>')
                .attr('type', 'hidden')
                .attr('data-validate-submit-hidden', '')
                .attr('name', this.$submitButton.attr('name'))
                .val(this.$submitButton.val())
                .appendTo(this.$form);
        }

        // Submit form
        this.$form.off('submit.validate').submit();
    },

    // ---
    // Useful APIs which aren't used internally
    // ---

    /**
     * Get the list of invalid fields
     *
     * @returns {jQuery[]}
     */
    getInvalidFields: function() {
        return this.$invalidFields;
    },

    /**
     * Returns the clicked submit button
     *
     * @returns {jQuery}
     */
    getSubmitButton: function() {
        return this.$submitButton;
    },

    /**
     * Get the error messages
     *
     * @param {String|jQuery} [field] The field name or field element
     * If the field is not defined, the method returns all error messages of all fields
     * @param {String} [validator] The name of validator
     * If the validator is not defined, the method returns error messages of all validators
     * @returns {String[]}
     */
    getMessages: function(field, validator) {
        var that     = this,
            messages = [],
            $fields  = $([]);

        switch (true) {
            case (field && 'object' === typeof field):
                $fields = field;
                break;
            case (field && 'string' === typeof field):
                var f = this.getFieldElements(field);
                if (f.length > 0) {
                    var type = f.attr('type');
                    $fields = ('radio' === type || 'checkbox' === type) ? f.eq(0) : f;
                }
                break;
            default:
                $fields = this.$invalidFields;
                break;
        }

        var filter = validator ? '[data-validate-validator="' + validator + '"]' : '';
        $fields.each(function() {
            messages = messages.concat(
                $(this)
                    .data('validate.messages')
                    .find('[class|=help][data-validate-for="' + $(this).attr('data-validate-field') + '"][data-validate-result="' + that.STATUS_INVALID + '"]' + filter)
                    .map(function() {
                        var v = $(this).attr('data-validate-validator'),
                            f = $(this).attr('data-validate-for');
                        return (that.options.fields[f].validators[v].enabled === false) ? '' : $(this).html();
                    })
                    .get()
            );
        });

        return messages;
    },

    /**
     * Update the option of a specific validator
     *
     * @param {String|jQuery} field The field name or field element
     * @param {String} validator The validator name
     * @param {String} option The option name
     * @param {String} value The value to set
     * @returns {Validator}
     */
    updateOption: function(field, validator, option, value) {
        if ('object' === typeof field) {
            field = field.attr('data-validate-field');
        }
        if (this.options.fields[field] && this.options.fields[field].validators[validator]) {
            this.options.fields[field].validators[validator][option] = value;
            this.updateStatus(field, this.STATUS_NOT_VALIDATED, validator);
        }

        return this;
    },

    /**
     * Add a new field
     *
     * @param {String|jQuery} field The field name or field element
     * @param {Object} [options] The validator rules
     * @returns {Validator}
     */
    addField: function(field, options) {
        var fields = $([]);
        switch (typeof field) {
            case 'object':
                fields = field;
                field  = field.attr('data-validate-field') || field.attr('name');
                break;
            case 'string':
                delete this._cacheFields[field];
                fields = this.getFieldElements(field);
                break;
            default:
                break;
        }

        fields.attr('data-validate-field', field);

        var type  = fields.attr('type'),
            total = ('radio' === type || 'checkbox' === type) ? 1 : fields.length;

        for (var i = 0; i < total; i++) {
            var $field = fields.eq(i);

            // Try to parse the options from HTML attributes
            var opts = this._parseOptions($field);
            opts = (opts === null) ? options : $.extend(true, options, opts);

            this.options.fields[field] = $.extend(true, this.options.fields[field], opts);

            // Update the cache
            this._cacheFields[field] = this._cacheFields[field] ? this._cacheFields[field].add($field) : $field;

            // Init the element
            this._initField(('checkbox' === type || 'radio' === type) ? field : $field);
        }

        // this.disableSubmitButtons(false);
        // Trigger an event
        this.$form.trigger($.Event(this.options.events.fieldAdded), {
            field: field,
            element: fields,
            options: this.options.fields[field]
        });

        return this;
    },

    /**
     * Remove a given field
     *
     * @param {String|jQuery} field The field name or field element
     * @returns {Validator}
     */
    removeField: function(field) {
        var fields = $([]);
        switch (typeof field) {
            case 'object':
                fields = field;
                field  = field.attr('data-validate-field') || field.attr('name');
                fields.attr('data-validate-field', field);
                break;
            case 'string':
                fields = this.getFieldElements(field);
                break;
            default:
                break;
        }

        if (fields.length === 0) {
            return this;
        }

        var type  = fields.attr('type'),
            total = ('radio' === type || 'checkbox' === type) ? 1 : fields.length;

        for (var i = 0; i < total; i++) {
            var $field = fields.eq(i);

            // Remove from the list of invalid fields
            this.$invalidFields = this.$invalidFields.not($field);

            // Update the cache
            this._cacheFields[field] = this._cacheFields[field].not($field);
        }

        if (!this._cacheFields[field] || this._cacheFields[field].length === 0) {
            delete this.options.fields[field];
        }
        if ('checkbox' === type || 'radio' === type) {
            this._initField(field);
        }

        // this.disableSubmitButtons(false);
        // Trigger an event
        this.$form.trigger($.Event(this.options.events.fieldRemoved), {
            field: field,
            element: fields
        });

        return this;
    },

    /**
     * Reset given field
     *
     * @param {String|jQuery} field The field name or field element
     * @param {Boolean} [resetValue] If true, the method resets field value to empty or remove checked/selected attribute (for radio/checkbox)
     * @returns {Validator}
     */
    resetField: function(field, resetValue) {
        var $fields = $([]);
        switch (typeof field) {
            case 'object':
                $fields = field;
                field   = field.attr('data-validate-field');
                break;
            case 'string':
                $fields = this.getFieldElements(field);
                break;
            default:
                break;
        }

        var total = $fields.length;
        if (this.options.fields[field]) {
            for (var i = 0; i < total; i++) {
                for (var validator in this.options.fields[field].validators) {
                    $fields.eq(i).removeData('validate.dfs.' + validator);
                }
            }
        }

        // Mark field as not validated yet
        this.updateStatus(field, this.STATUS_NOT_VALIDATED);

        if (resetValue) {
            var type = $fields.attr('type');
            ('radio' === type || 'checkbox' === type) ? $fields.removeAttr('checked').removeAttr('selected') : $fields.val('');
        }

        return this;
    },

    /**
     * Reset the form
     *
     * @param {Boolean} [resetValue] If true, the method resets field value to empty or remove checked/selected attribute (for radio/checkbox)
     * @returns {Validator}
     */
    resetForm: function(resetValue) {
        for (var field in this.options.fields) {
            this.resetField(field, resetValue);
        }

        this.$invalidFields = $([]);
        this.$submitButton  = null;

        // Enable submit buttons
        // this.disableSubmitButtons(false);

        return this;
    },

    /**
     * Revalidate given field
     * It's used when you need to revalidate the field which its value is updated by other plugin
     *
     * @param {String|jQuery} field The field name of field element
     * @returns {Validator}
     */
    revalidateField: function(field) {
        this.updateStatus(field, this.STATUS_NOT_VALIDATED)
            .validateField(field);

        return this;
    },

    /**
     * Enable/Disable all validators to given field
     *
     * @param {String} field The field name
     * @param {Boolean} enabled Enable/Disable field validators
     * @param {String} [validatorName] The validator name. If null, all validators will be enabled/disabled
     * @returns {Validator}
     */
    enableFieldValidators: function(field, enabled, validatorName) {
        var validators = this.options.fields[field].validators;

        // Enable/disable particular validator
        if (validatorName
            && validators
            && validators[validatorName] && validators[validatorName].enabled !== enabled)
        {
            this.options.fields[field].validators[validatorName].enabled = enabled;
            this.updateStatus(field, this.STATUS_NOT_VALIDATED, validatorName);
        }
        // Enable/disable all validators
        else if (!validatorName && this.options.fields[field].enabled !== enabled) {
            this.options.fields[field].enabled = enabled;
            for (var v in validators) {
                this.enableFieldValidators(field, enabled, v);
            }
        }

        return this;
    },

    /**
     * Some validators have option which its value is dynamic.
     * For example, the zipCode validator has the country option which might be changed dynamically by a select element.
     *
     * @param {jQuery|String} field The field name or element
     * @param {String|Function} option The option which can be determined by:
     * - a string
     * - name of field which defines the value
     * - name of function which returns the value
     * - a function returns the value
     *
     * The callback function has the format of
     *      callback: function(value, validator, $field) {
     *          // value is the value of field
     *          // validator is the Validator instance
     *          // $field is the field element
     *      }
     *
     * @returns {String}
     */
    getDynamicOption: function(field, option) {
        var $field = ('string' === typeof field) ? this.getFieldElements(field) : field,
            value  = $field.val();

        // Option can be determined by
        // ... a function
        if ('function' === typeof option) {
            return $.fn.Validator.helpers.call(option, [value, this, $field]);
        }
        // ... value of other field
        else if ('string' === typeof option) {
            var $f = this.getFieldElements(option);
            if ($f.length) {
                return $f.val();
            }
            // ... return value of callback
            else {
                return $.fn.Validator.helpers.call(option, [value, this, $field]) || option;
            }
        }

        return null;
    },

    /**
     * Destroy the plugin
     * It will remove all error messages, feedback icons and turn off the events
     */
    destroy: function() {
        var field, fields, $field, validator, $icon, group;
        for (field in this.options.fields) {
            fields    = this.getFieldElements(field);
            group     = this.options.fields[field].group || this.options.group;
            for (var i = 0; i < fields.length; i++) {
                $field = fields.eq(i);
                $field
                    // Remove all error messages
                    .data('validate.messages')
                        .find('[class|=help][data-validate-validator][data-validate-for="' + field + '"]').remove().end()
                        .end()
                    .removeData('validate.messages')
                    // Remove feedback classes
                    .parents(group)
                        .removeClass('has-feedback has-error has-success')
                        .end()
                    // Turn off events
                    .off('.validate')
                    .removeAttr('data-validate-field');

                // Remove feedback icons, tooltip/popover container
                $icon = $field.data('validate.icon');
                if ($icon) {
                    var container = ('function' === typeof (this.options.fields[field].container || this.options.container)) ? (this.options.fields[field].container || this.options.container).call(this, $field, this) : (this.options.fields[field].container || this.options.container);
                    switch (container) {
                        case 'tooltip':
                            $icon.tooltip('destroy').remove();
                            break;
                        case 'popover':
                            $icon.popover('destroy').remove();
                            break;
                        default:
                            $icon.remove();
                            break;
                    }
                }
                $field.removeData('validate.icon');

                for (validator in this.options.fields[field].validators) {
                    if ($field.data('validate.dfs.' + validator)) {
                        $field.data('validate.dfs.' + validator).reject();
                    }
                    $field.removeData('validate.result.' + validator)
                        .removeData('validate.response.' + validator)
                        .removeData('validate.dfs.' + validator);

                    // Destroy the validator
                    if ('function' === typeof $.fn.Validator.validators[validator].destroy) {
                        $.fn.Validator.validators[validator].destroy(this, $field, this.options.fields[field].validators[validator]);
                    }
                }
            }
        }

        // this.disableSubmitButtons(false);   // Enable submit buttons
        this.$hiddenButton.remove();        // Remove the hidden button

        this.$form
            .removeClass(this.options.elementClass)
            .off('.validate')
            .removeData('Validator')
            // Remove generated hidden elements
            .find('[data-validate-submit-hidden]').remove().end()
            .find('[type="submit"]').off('click.validate');
    }
};

// Plugin definition
$.fn.Validator = function(option) {
    var params = arguments;
    return this.each(function() {
        var $this   = $(this),
            data    = $this.data('Validator'),
            options = 'object' === typeof option && option;
        if (!data) {
            data = new Validator(this, options);
            $this.data('Validator', data);
        }

        // Allow to call plugin method
        if ('string' === typeof option) {
            data[option].apply(data, Array.prototype.slice.call(params, 1));
        }
    });
};

// The default options
// Sorted in alphabetical order
$.fn.Validator.DEFAULT_OPTIONS = {
    // The first invalid field will be focused automatically
    autoFocus: true,

    //The error messages container. It can be:
    // - 'tooltip' if you want to use Bootstrap tooltip to show error messages
    // - 'popover' if you want to use Bootstrap popover to show error messages
    // - a CSS selector indicating the container
    // In the first two cases, since the tooltip/popover should be small enough, the plugin only shows only one error message
    // You also can define the message container for particular field
    container: null,

    // The form CSS class
    elementClass: 'validate-form',

    // Use custom event name to avoid window.onerror being invoked by jQuery
    // See https://github.com/nghuuphuoc/bootstrapvalidator/issues/630
    events: {
        formInit: 'init.form.validate',
        formError: 'error.form.validate',
        formSuccess: 'success.form.validate',
        fieldAdded: 'added.field.validate',
        fieldRemoved: 'removed.field.validate',
        fieldInit: 'init.field.validate',
        fieldError: 'error.field.validate',
        fieldSuccess: 'success.field.validate',
        fieldStatus: 'status.field.validate',
        validatorError: 'error.validator.validate',
        validatorSuccess: 'success.validator.validate'
    },

    // Indicate fields which won't be validated
    // By default, the plugin will not validate the following kind of fields:
    // - disabled
    // - hidden
    // - invisible
    //
    // The setting consists of jQuery filters. Accept 3 formats:
    // - A string. Use a comma to separate filter
    // - An array. Each element is a filter
    // - An array. Each element can be a callback function
    //      function($field, validator) {
    //          $field is jQuery object representing the field element
    //          validator is the Validator instance
    //          return true or false;
    //      }
    //
    // The 3 following settings are equivalent:
    //
    // 1) ':disabled, input[type=hidden], :not(:visible)'
    // 2) [':disabled', 'input[type=hidden]', ':not(:visible)']
    // 3) [':disabled', 'input[type=hidden]', function($field) {
    //        return !$field.is(':visible');
    //    }]
    excluded: [':disabled', 'input[type=hidden]', ':not(:visible)'],

    // Map the field name with validator rules
    fields: null,

    // Shows ok/error/loading icons based on the field validity.
    // This feature requires Bootstrap v3.1.0 or later (http://getbootstrap.com/css/#forms-control-validation).
    // Since Bootstrap doesn't provide any methods to know its version, this option cannot be on/off automatically.
    // In other word, to use this feature you have to upgrade your Bootstrap to v3.1.0 or later.
    //
    // Examples:
    // - Use Glyphicons icons:
    //  icons: {
    //      valid: 'glyphicon glyphicon-ok',
    //      invalid: 'glyphicon glyphicon-remove',
    //      validating: 'glyphicon glyphicon-refresh'
    //  }
    // - Use FontAwesome icons:
    //  icons: {
    //      valid: 'fa fa-check',
    //      invalid: 'fa fa-times',
    //      validating: 'fa fa-refresh'
    //  }
    icons: {
        valid:      null,
        invalid:    null,
        validating: null
    },

    // The CSS selector for indicating the element consists the field
    // By default, each field is placed inside the <div class="form-group"></div>
    // You should adjust this option if your form group consists of many fields which not all of them need to be validated
    group: '.form-group',

    // Live validating option
    // Can be one of 3 values:
    // - enabled: The plugin validates fields as soon as they are changed
    // - disabled: Disable the live validating. The error messages are only shown after the form is submitted
    // - submitted: The live validating is enabled after the form is submitted
    live: 'enabled',

    // Default invalid message
    message: 'This value is not valid',

    // The submit buttons selector
    // These buttons will be disabled to prevent the valid form from multiple submissions
    submitButtons: '[type="submit"]',

    // The field will not be live validated if its length is less than this number of characters
    threshold: null,

    // Whether to be verbose when validating a field or not.
    // Possible values:
    // - true:  when a field has multiple validators, all of them will be checked, and respectively - if errors occur in
    //          multiple validators, all of them will be displayed to the user
    // - false: when a field has multiple validators, validation for this field will be terminated upon the first encountered error.
    //          Thus, only the very first error message related to this field will be displayed to the user
    verbose: true
};

// Available validators
$.fn.Validator.validators  = {};

// i18n
$.fn.Validator.i18n        = {};

$.fn.Validator.Constructor = Validator;

// Helper methods, which can be used in validator class
$.fn.Validator.helpers = {
    /**
     * Execute a callback function
     *
     * @param {String|Function} functionName Can be
     * - name of global function
     * - name of namespace function (such as A.B.C)
     * - a function
     * @param {Array} args The callback arguments
     */
    call: function(functionName, args) {
        if ('function' === typeof functionName) {
            return functionName.apply(this, args);
        } else if ('string' === typeof functionName) {
            if ('()' === functionName.substring(functionName.length - 2)) {
                functionName = functionName.substring(0, functionName.length - 2);
            }
            var ns      = functionName.split('.'),
                func    = ns.pop(),
                context = window;
            for (var i = 0; i < ns.length; i++) {
                context = context[ns[i]];
            }
            return context[func].apply(this, args);
        }
    },

    /**
     * Format a string
     * It's used to format the error message
     * format('The field must between %s and %s', [10, 20]) = 'The field must between 10 and 20'
     *
     * @param {String} message
     * @param {Array} parameters
     * @returns {String}
     */
    format: function(message, parameters) {
        if (!$.isArray(parameters)) {
            parameters = [parameters];
        }

        for (var i in parameters) {
            message = message.replace('%s', parameters[i]);
        }

        return message;
    },

    /**
     * Validate a date
     *
     * @param {Number} year The full year in 4 digits
     * @param {Number} month The month number
     * @param {Number} day The day number
     * @param {Boolean} [notInFuture] If true, the date must not be in the future
     * @returns {Boolean}
     */
    date: function(year, month, day, notInFuture) {
        if (isNaN(year) || isNaN(month) || isNaN(day)) {
            return false;
        }
        if (day.length > 2 || month.length > 2 || year.length > 4) {
            return false;
        }

        day   = parseInt(day, 10);
        month = parseInt(month, 10);
        year  = parseInt(year, 10);

        if (year < 1000 || year > 9999 || month <= 0 || month > 12) {
            return false;
        }
        var numDays = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
        // Update the number of days in Feb of leap year
        if (year % 400 === 0 || (year % 100 !== 0 && year % 4 === 0)) {
            numDays[1] = 29;
        }

        // Check the day
        if (day <= 0 || day > numDays[month - 1]) {
            return false;
        }

        if (notInFuture === true) {
            var currentDate  = new Date(),
                currentYear  = currentDate.getFullYear(),
                currentMonth = currentDate.getMonth(),
                currentDay   = currentDate.getDate();
            return (year < currentYear
                    || (year === currentYear && month - 1 < currentMonth)
                    || (year === currentYear && month - 1 === currentMonth && day < currentDay));
        }

        return true;
    },

    /**
     * Implement Luhn validation algorithm
     * Credit to https://gist.github.com/ShirtlessKirk/2134376
     *
     * @see http://en.wikipedia.org/wiki/Luhn
     * @param {String} value
     * @returns {Boolean}
     */
    luhn: function(value) {
        var length  = value.length,
            mul     = 0,
            prodArr = [[0, 1, 2, 3, 4, 5, 6, 7, 8, 9], [0, 2, 4, 6, 8, 1, 3, 5, 7, 9]],
            sum     = 0;

        while (length--) {
            sum += prodArr[mul][parseInt(value.charAt(length), 10)];
            mul ^= 1;
        }

        return (sum % 10 === 0 && sum > 0);
    }
};

$.fn.Validator.validators.alpha = {

    /**
     * Return true if the input value contains alpha only
     *
     * @param {Validator} validator The validator plugin instance
     * @param {jQuery} $field Field element
     * @param {Object} [options]
     * @returns {Boolean}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        return value === '' || /^[a-zA-Z]+$/.test(value);
    }
};

$.fn.Validator.validators.alphadigits = {

    /**
     * Return true if the input value contains alpha or digits only
     *
     * @returns {Boolean}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        return value === '' || /^[a-zA-Z\d]+$/.test(value);
    }
};

$.fn.Validator.validators.between = {
    html5Attributes: {
        message: 'message',
        min: 'min',
        max: 'max',
        inclusive: 'inclusive'
    },

    enableByHtml5: function($field) {
        if ('range' === $field.attr('type')) {
            return {
                min: $field.attr('min'),
                max: $field.attr('max')
            };
        }

        return false;
    },

    /**
     * Return true if the input value is between (strictly or not) two given numbers
     *
     * - min
     * - max
     *
     * The min, max keys define the number which the field value compares to. min, max can be
     *      - A number
     *      - Name of field which its value defines the number
     *      - Name of callback function that returns the number
     *      - A callback function that returns the number
     *
     * - inclusive [optional]: Can be true or false. Default is true
     * - message: The invalid message
     * @returns {Boolean|Object}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        value = this._format(value);

        var min      = $.isNumeric(options.min) ? options.min : validator.getDynamicOption($field, options.min),
            max      = $.isNumeric(options.max) ? options.max : validator.getDynamicOption($field, options.max),
            minValue = this._format(min),
            maxValue = this._format(max);

        value = Number(value);
        return (options.inclusive === true || options.inclusive === undefined)
            ? {
                valid: value >= minValue && value <= maxValue,
                message: $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.between['default'], [min, max])
            }
            : {
                valid: value > minValue  && value <  maxValue,
                message: $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.between.notInclusive, [min, max])
            };
    },

    _format: function(value) {
        return (value + '').replace(',', '.');
    }
};

$.fn.Validator.validators.callback = {
    html5Attributes: {
        message: 'message',
        callback: 'callback'
    },

    /**
     * Return result from the callback method
     *
     * - callback: The callback method that passes 2 parameters:
     *      callback: function(fieldValue, validator, $field) {
     *          // fieldValue is the value of field
     *          // validator is instance of Validator
     *          // $field is the field element
     *      }
     * - message: The invalid message
     * @returns {Deferred}
     */
    validate: function(validator, $field, options) {
        var value  = $field.val(),
            dfd    = new $.Deferred(),
            result = { valid: true };

        if (options.callback) {
            var response = $.fn.Validator.helpers.call(options.callback, [value, validator, $field]);
            result = ('boolean' === typeof response) ? { valid: response } :  response;
        }

        dfd.resolve($field, 'callback', result);
        return dfd;
    }
};

$.fn.Validator.validators.choice = {
    html5Attributes: {
        message: 'message',
        min: 'min',
        max: 'max'
    },

    /**
     * Check if the number of checked boxes are less or more than a given number
     *
     * - min
     * - max
     *
     * At least one of two keys is required
     * The min, max keys define the number which the field value compares to. min, max can be
     *      - A number
     *      - Name of field which its value defines the number
     *      - Name of callback function that returns the number
     *      - A callback function that returns the number
     *
     * - message: The invalid message
     * @returns {Object}
     */
    validate: function(validator, $field, options) {
        var numChoices = $field.is('select')
                        ? validator.getFieldElements($field.attr('data-validate-field')).find('option').filter(':selected').length
                        : validator.getFieldElements($field.attr('data-validate-field')).filter(':checked').length,
            min        = options.min ? ($.isNumeric(options.min) ? options.min : validator.getDynamicOption($field, options.min)) : null,
            max        = options.max ? ($.isNumeric(options.max) ? options.max : validator.getDynamicOption($field, options.max)) : null,
            isValid    = true,
            message    = options.message || $.fn.Validator.i18n.choice['default'];

        if ((min && numChoices < parseInt(min, 10)) || (max && numChoices > parseInt(max, 10))) {
            isValid = false;
        }

        switch (true) {
            case (!!min && !!max):
                message = $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.choice.between, [parseInt(min, 10), parseInt(max, 10)]);
                break;

            case (!!min):
                message = $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.choice.less, parseInt(min, 10));
                break;

            case (!!max):
                message = $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.choice.more, parseInt(max, 10));
                break;

            default:
                break;
        }

        return { valid: isValid, message: message };
    }
};

$.fn.Validator.validators.cnchar = {

    /**
     * Return true if the input value contains chinese characters only
     *
     * @returns {Boolean}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();

        return value === '' || /^[\u4e00-\u9fa5]+$/.test(value);
    }
};

$.fn.Validator.validators.color = {
    enableByHtml5: function($field) {
        return ('color' === $field.attr('type'));
    },

    /**
     * Return true if the input value is a valid hex color
     *
     * - message: The invalid message
     * @returns {Boolean}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }
        return /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i.test(value);
    }
};

$.fn.Validator.validators.date = {
    html5Attributes: {
        message: 'message',
        format: 'format',
        min: 'min',
        max: 'max',
        separator: 'separator'
    },

    /**
     * Return true if the input value is valid date
     *
     * - message: The invalid message
     * - min: the minimum date
     * - max: the maximum date
     * - separator: Use to separate the date, month, and year.
     * By default, it is /
     * - format: The date format. Default is MM/DD/YYYY
     * The format can be:
     *
     * i) date: Consist of DD, MM, YYYY parts which are separated by the separator option
     * ii) date and time:
     * The time can consist of h, m, s parts which are separated by :
     * ii) date, time and A (indicating AM or PM)
     * @returns {Boolean|Object}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        options.format = options.format || 'MM/DD/YYYY';

        // #683: Force the format to YYYY-MM-DD as the default browser behaviour when using type="date" attribute
        if ($field.attr('type') === 'date') {
            options.format = 'YYYY-MM-DD';
        }

        var formats    = options.format.split(' '),
            dateFormat = formats[0],
            timeFormat = (formats.length > 1) ? formats[1] : null,
            amOrPm     = (formats.length > 2) ? formats[2] : null,
            sections   = value.split(' '),
            date       = sections[0],
            time       = (sections.length > 1) ? sections[1] : null;

        if (formats.length !== sections.length) {
            return {
                valid: false,
                message: options.message || $.fn.Validator.i18n.date['default']
            };
        }

        // Determine the separator
        var separator = options.separator;
        if (!separator) {
            separator = (date.indexOf('/') !== -1) ? '/' : ((date.indexOf('-') !== -1) ? '-' : null);
        }
        if (separator === null || date.indexOf(separator) === -1) {
            return {
                valid: false,
                message: options.message || $.fn.Validator.i18n.date['default']
            };
        }

        // Determine the date
        date       = date.split(separator);
        dateFormat = dateFormat.split(separator);
        if (date.length !== dateFormat.length) {
            return {
                valid: false,
                message: options.message || $.fn.Validator.i18n.date['default']
            };
        }

        var year  = date[$.inArray('YYYY', dateFormat)],
            month = date[$.inArray('MM', dateFormat)],
            day   = date[$.inArray('DD', dateFormat)];

        if (!year || !month || !day || year.length !== 4) {
            return false;
        }

        // Determine the time
        var minutes = null, hours = null, seconds = null;
        if (timeFormat) {
            timeFormat = timeFormat.split(':');
            time       = time.split(':');

            if (timeFormat.length !== time.length) {
                return false;
            }

            hours   = time.length > 0 ? time[0] : null;
            minutes = time.length > 1 ? time[1] : null;
            seconds = time.length > 2 ? time[2] : null;

            // Validate seconds
            if (seconds) {
                if (isNaN(seconds) || seconds.length > 2) {
                    return false;
                }
                seconds = parseInt(seconds, 10);
                if (seconds < 0 || seconds > 60) {
                    return false;
                }
            }

            // Validate hours
            if (hours) {
                 if (isNaN(hours) || hours.length > 2) {
                    return false;
                }
                hours = parseInt(hours, 10);
                if (hours < 0 || hours >= 24 || (amOrPm && hours > 12)) {
                    return false;
                }
            }

            // Validate minutes
            if (minutes) {
                if (isNaN(minutes) || minutes.length > 2) {
                    return false;
                }
                minutes = parseInt(minutes, 10);
                if (minutes < 0 || minutes > 59) {
                    return false;
                }
            }
        }

        // Validate day, month, and year
        var valid   = $.fn.Validator.helpers.date(year, month, day),
            message = $.fn.Validator.i18n.date['default'];

        // declare the date, min and max objects
        var min       = null,
            max       = null,
            minOption = options.min,
            maxOption = options.max;

        if(minOption) {
            if(isNaN(Date.parse(minOption))) {
                minOption = validator.getDynamicOption($field, minOption);
            }
            min = this._parseDate(minOption, dateFormat, separator);
        }

        if(maxOption) {
            if(isNaN(Date.parse(maxOption))) {
                maxOption = validator.getDynamicOption($field, maxOption);
            }
            max = this._parseDate(maxOption, dateFormat, separator);
        }

        date = new Date(year, month, day, hours, minutes, seconds);

        switch(true) {
            case(minOption && !maxOption && valid):
                valid   = date.getTime() >= min.getTime();
                message = options.message || $.fn.Validator.helpers.format($.fn.Validator.i18n.date.min, minOption);
                break;

            case(maxOption && !minOption && valid):
                valid   = date.getTime() <= max.getTime();
                message = options.message || $.fn.Validator.helpers.format($.fn.Validator.i18n.date.max, maxOption);
                break;

            case(maxOption && minOption && valid):
                valid   = date.getTime() <= max.getTime() && date.getTime() >= min.getTime();
                message = options.message || $.fn.Validator.helpers.format($.fn.Validator.i18n.date.range, [minOption, maxOption]);
                break;

            default:
                break;
        }

        return {
            valid: valid,
            message: message
        };
    },

    /**
     * Return a date object after parsing the date string
     *
     * @param {String} date   The date string to parse
     * @param {String} format The date format
     * The format can be:
     *   - date: Consist of DD, MM, YYYY parts which are separated by the separator option
     *   - date and time:
     *     The time can consist of h, m, s parts which are separated by :
     * @param {String} separator The separator used to separate the date, month, and year
     * @returns {Date}
     */
    _parseDate: function(date, format, separator) {
        var minutes = 0, hours = 0, seconds = 0,
            sections    = date.split(' '),
            dateSection = sections[0],
            timeSection = (sections.length > 1) ? sections[1] : null;

        dateSection = dateSection.split(separator);
        var year  = dateSection[$.inArray('YYYY', format)],
            month = dateSection[$.inArray('MM', format)],
            day   = dateSection[$.inArray('DD', format)];
        if (timeSection) {
            timeSection = timeSection.split(':');
            hours       = timeSection.length > 0 ? timeSection[0] : null;
            minutes     = timeSection.length > 1 ? timeSection[1] : null;
            seconds     = timeSection.length > 2 ? timeSection[2] : null;
        }

        return new Date(year, month, day, hours, minutes, seconds);
    }
};

$.fn.Validator.validators.different = {
    html5Attributes: {
        message: 'message',
        field: 'field'
    },

    /**
     * Return true if the input value is different with given field's value
     *
     * - field: The name of field that will be used to compare with current one
     * - message: The invalid message
     * @returns {Boolean}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        var fields  = options.field.split(','),
            isValid = true;

        for (var i = 0; i < fields.length; i++) {
            var compareWith = validator.getFieldElements(fields[i]);
            if (compareWith == null || compareWith.length === 0) {
                continue;
            }

            var compareValue = compareWith.val();
            if (value === compareValue) {
                isValid = false;
            } else if (compareValue !== '') {
                validator.updateStatus(compareWith, validator.STATUS_VALID, 'different');
            }
        }

        return isValid;
    }
};

$.fn.Validator.validators.digits = {
    /**
     * Return true if the input value contains digits only
     *
     * @param {BootstrapValidator} validator Validate plugin instance
     * @param {jQuery} $field Field element
     * @param {Object} [options]
     * @returns {Boolean}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        return /^\d+$/.test(value);
    }
};

$.fn.Validator.validators.email = {
    html5Attributes: {
        message: 'message',
        multiple: 'multiple',
        separator: 'separator'
    },

    enableByHtml5: function($field) {
        return ('email' === $field.attr('type'));
    },

    /**
     * Return true if and only if the input value is a valid email address
     * - multiple: Allow multiple email addresses, separated by a comma or semicolon; default is false.
     * - separator: Regex for character or characters expected as separator between addresses; default is comma /[,;]/, i.e. comma or semicolon.
     * @returns {Boolean}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        // Email address regular expression
        // http://stackoverflow.com/questions/46155/validate-email-address-in-javascript
        var emailRegExp   = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@(?:[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/,
        /* or you can use:  /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(?:([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}))$/ */
            allowMultiple = options.multiple === true || options.multiple === 'true';

        if (allowMultiple) {
            var separator = options.separator || /[,;]/,
                emails = this._splitEmail(value, separator);

            for (var i = 0; i < emails.length; i++) {
                if (!emailRegExp.test(emails[i])) {
                    return false;
                }
            }

            return true;
        } else {
            return emailRegExp.test(value);
        }
    },

    _splitEmail: function(email, separator) {
        var quotedFragments     = email.split(/"/),
            quotedFragmentCount = quotedFragments.length,
            emailArray   = [],
            nextEmail    = '';

        for (var i = 0; i < quotedFragmentCount; i++) {
            if (i % 2 === 0) {
                var splitEmailFragments     = quotedFragments[i].split(separator),
                    splitEmailFragmentCount = splitEmailFragments.length;

                if (splitEmailFragmentCount === 1) {
                    nextEmail += splitEmailFragments[0];
                } else {
                    emailArray.push(nextEmail + splitEmailFragments[0]);

                    for (var j = 1; j < splitEmailFragmentCount - 1; j++) {
                        emailArray.push(splitEmailFragments[j]);
                    }
                    nextEmail = splitEmailFragments[splitEmailFragmentCount - 1];
                }
            } else {
                nextEmail += '"' + quotedFragments[i];
                if (i < quotedFragmentCount - 1) {
                    nextEmail += '"';
                }
            }
        }

        emailArray.push(nextEmail);
        return emailArray;
    }
};

$.fn.Validator.validators.equalto = {
    html5Attributes: {
        message: 'message',
        field: 'field'
    },

    /**
     * Check if input value equals to value of particular one
     *
     * - field: The name of field that will be used to compare with current one
     * @returns {Boolean}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        var compareWith = validator.getFieldElements(options.field);
        if (compareWith === null) {
            return true;
        }

        if (value === compareWith.val()) {
            validator.updateStatus(options.field, validator.STATUS_VALID, 'equalto');
            return true;
        } else {
            return false;
        }
    }
};

$.fn.Validator.validators.file = {
    html5Attributes: {
        extension: 'extension',
        maxfiles: 'maxFiles',
        minfiles: 'minFiles',
        maxsize: 'maxSize',
        minsize: 'minSize',
        maxtotalsize: 'maxTotalSize',
        mintotalsize: 'minTotalSize',
        message: 'message',
        type: 'type'
    },

    /**
     * Validate upload file. Use HTML 5 API if the browser supports
     *
     * - extension: The allowed extensions, separated by a comma
     * - maxFiles: The maximum number of files
     * - minFiles: The minimum number of files
     * - maxSize: The maximum size in bytes
     * - minSize: The minimum size in bytes
     * - maxTotalSize: The maximum size in bytes for all files
     * - minTotalSize: The minimum size in bytes for all files
     * - message: The invalid message
     * - type: The allowed MIME type, separated by a comma
     * @returns {Boolean}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        var ext,
            extensions = options.extension ? options.extension.toLowerCase().split(',') : null,
            types      = options.type      ? options.type.toLowerCase().split(',')      : null,
            html5      = (window.File && window.FileList && window.FileReader);

        if (html5) {
            // Get FileList instance
            var files     = $field.get(0).files,
                total     = files.length,
                totalSize = 0;

            if ((options.maxFiles && total > parseInt(options.maxFiles, 10))        // Check the maxFiles
                || (options.minFiles && total < parseInt(options.minFiles, 10)))    // Check the minFiles
            {
                return false;
            }

            for (var i = 0; i < total; i++) {
                totalSize += files[i].size;
                ext        = files[i].name.substr(files[i].name.lastIndexOf('.') + 1);

                if ((options.minSize && files[i].size < parseInt(options.minSize, 10))                      // Check the minSize
                    || (options.maxSize && files[i].size > parseInt(options.maxSize, 10))                   // Check the maxSize
                    || (extensions && $.inArray(ext.toLowerCase(), extensions) === -1)                      // Check file extension
                    || (files[i].type && types && $.inArray(files[i].type.toLowerCase(), types) === -1))    // Check file type
                {
                    return false;
                }
            }

            if ((options.maxTotalSize && totalSize > parseInt(options.maxTotalSize, 10))        // Check the maxTotalSize
                || (options.minTotalSize && totalSize < parseInt(options.minTotalSize, 10)))    // Check the minTotalSize
            {
                return false;
            }
        } else {
            // Check file extension
            ext = value.substr(value.lastIndexOf('.') + 1);
            if (extensions && $.inArray(ext.toLowerCase(), extensions) === -1) {
                return false;
            }
        }

        return true;
    }
};

$.fn.Validator.validators.gt = {
    html5Attributes: {
        message: 'message',
        value: 'value',
        inclusive: 'inclusive'
    },

    enableByHtml5: function($field) {
        var type = $field.attr('type'),
            min  = $field.attr('min');
        if (min && type !== 'date') {
            return {
                value: min
            };
        }

        return false;
    },

    /**
     * Return true if the input value is greater than or equals to given number
     *
     * - value: Define the number to compare with. It can be
     *      - A number
     *      - Name of field which its value defines the number
     *      - Name of callback function that returns the number
     *      - A callback function that returns the number
     *
     * - inclusive [optional]: Can be true or false. Default is true
     * - message: The invalid message
     * @returns {Boolean|Object}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        value = this._format(value);

        var compareTo      = $.isNumeric(options.value) ? options.value : validator.getDynamicOption($field, options.value),
            compareToValue = this._format(compareTo);

        value = Number(value);
        return (options.inclusive === true || options.inclusive === undefined)
                ? {
                    valid: value >= compareToValue,
                    message: $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.gt['default'], compareTo)
                }
                : {
                    valid: value > compareToValue,
                    message: $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.gt.notInclusive, compareTo)
                };
    },

    _format: function(value) {
        return (value + '').replace(',', '.');
    }
};

$.fn.Validator.validators.id = {
    html5Attributes: {
        message: 'message',
        country: 'country'
    },

    // Supported country codes
    COUNTRY_CODES: ['CN'],

    /**
     * Validate identification number in different countries
     *
     * @see http://en.wikipedia.org/wiki/National_identification_number
     * - message: The invalid message
     * - country: The ISO 3166-1 country code. It can be
     *      - One of country code defined in COUNTRY_CODES
     *      - Name of field which its value defines the country code
     *      - Name of callback function that returns the country code
     *      - A callback function that returns the country code
     * @returns {Boolean|Object}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        var country = options.country || this.COUNTRY_CODES[0];
        if (typeof country !== 'string' || $.inArray(country.toUpperCase(), this.COUNTRY_CODES) === -1) {
            // Determine the country code
            country = validator.getDynamicOption($field, country);
        }

        if ($.inArray(country, this.COUNTRY_CODES) === -1) {
            return { valid: false, message: $.fn.Validator.helpers.format($.fn.Validator.i18n.id.countryNotSupported, country) };
        }

        var method  = ['_', country.toLowerCase()].join('');
        return this[method](value)
                ? true
                : {
                    valid: false,
                    message: $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.id.country, $.fn.Validator.i18n.id.countries[country.toUpperCase()])
                };
    },

    /**
     * Validate Chinese ID
     *
     * @param {String} value The ID
     * @returns {Boolean}
     */

    /**
     * 身份证15位编码规则：dddddd yymmdd xx p
     * dddddd：地区码
     * yymmdd: 出生年月日
     * xx: 顺序类编码，无法确定
     * p: 性别，奇数为男，偶数为女
     *
     * 身份证18位编码规则：dddddd yyyymmdd xxx y
     * dddddd：地区码
     * yyyymmdd: 出生年月日
     * xxx:顺序类编码，无法确定，奇数为男，偶数为女
     * y: 校验码，该位数值可通过前17位计算获得
     *
     * 18位号码加权因子为(从右到左) Wi = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2,1 ]
     * 验证位 Y = [ 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ]
     * 校验位计算公式：Y_P = mod(∑(Ai×Wi),11)
     * i为身份证号码从右往左数的 2...18 位; Y_P为脚丫校验码所在校验码数组位置
     *
     */

    _cn: function(value) {

        var Wi = [ 7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2, 1 ]; // 加权因子
        var ValideCode = [ 1, 0, 10, 9, 8, 7, 6, 5, 4, 3, 2 ]; // 身份证验证位值.10代表x

        value = value.replace(/ /g, '');
        if (value.length == 15) {
            return isValidityBrithBy15IdCard(value);
        } else if (value.length == 18) {
            var a_idCard = value.split(''); // 得到身份证数组
            if(isValidityBrithBy18IdCard(value) && isTrueValidateCodeBy18IdCard(a_idCard)){
                return true;
            }else {
                return false;
            }
        } else {
            return false;
        }

        /**
         * 判断身份证号码为18位时最后的验证位是否正确
         * @param a_idCard 身份证号码数组
         * @return
         */
        function isTrueValidateCodeBy18IdCard(a_idCard) {
            var sum = 0; // 声明加权求和变量
            if (a_idCard[17].toLowerCase() == 'x') {
                a_idCard[17] = 10; // 将最后位为x的验证码替换为10方便后续操作
            }
            for ( var i = 0; i < 17; i++) {
                sum += Wi[i] * a_idCard[i]; // 加权求和
            }
            valCodePosition = sum % 11; // 得到验证码所位置
            if (a_idCard[17] == ValideCode[valCodePosition]) {
                return true;
            } else {
                return false;
            }
        }
        /**
         * 通过身份证判断是男是女
         * @param idCard 15/18位身份证号码
         * @return 'female'-女、'male'-男
         */
        /**
        function maleOrFemalByIdCard(idCard){
            idCard = idCard.replace(/ /g, ''); // 对身份证号码处理字符间空格。
            if(idCard.length == 15){
                if(idCard.substring(14,15) % 2 == 0){
                    return 'female';
                }else{
                    return 'male';
                }
            }else if(idCard.length == 18){
                if(idCard.substring(14,17) % 2 == 0){
                    return 'female';
                }else{
                    return 'male';
                }
            }else{
                return null;
            }
        }
        */
        /**
         * 验证18位数身份证号码中的生日是否是有效生日
         * @param idCard 18位书身份证字符串
         * @return
         */
        function isValidityBrithBy18IdCard(value){
            //截取身份证中的日期
            var year  = value.substr(6, 4),
                month = value.substr(10, 2),
                day   = value.substr(12, 2);

            if (!$.fn.Validator.helpers.date(year, month, day)) {
                return false;
            }else{
                return true;
            }
        }

        /**
        * 验证15位数身份证号码中的生日是否是有效生日
        * @param idCard15 15位书身份证字符串
        * @return
        */
        function isValidityBrithBy15IdCard(value){
            var year  = parseInt(value.substr(6, 2), 10) + 1900,
                month = value.substr(8, 2),
                day   = value.substr(10, 2);

            if (!$.fn.Validator.helpers.date(year, month, day)) {
                return false;
            }else{
                return true;
            }
        }

        // Validate the last check digit
        return $.fn.Validator.helpers.luhn(value);
    }
};

$.fn.Validator.validators.integer = {
    enableByHtml5: function($field) {
        return ('number' === $field.attr('type')) && ($field.attr('step') !== undefined) && ($field.attr('step') % 1 === 0);
    },

    /**
     * Return true if the input value is an integer
     *
     * - message: The invalid message
     * @returns {Boolean}
     */
    validate: function(validator, $field, options) {
        if (this.enableByHtml5($field) && $field.get(0).validity && $field.get(0).validity.badInput === true) {
            return false;
        }

        var value = $field.val();
        if (value === '') {
            return true;
        }
        return /^(?:-?(?:0|[1-9][0-9]*))$/.test(value);
    }
};

$.fn.Validator.validators.ip = {
    html5Attributes: {
        message: 'message',
        ipv4: 'ipv4',
        ipv6: 'ipv6'
    },

    /**
     * Return true if the input value is a IP address.
     *
     * - ipv4: Enable IPv4 validator, default to true
     * - ipv6: Enable IPv6 validator, default to true
     * - message: The invalid message
     * @returns {Boolean|Object}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }
        options = $.extend({}, { ipv4: true, ipv6: true }, options);

        var ipv4Regex = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/,
            ipv6Regex = /^\s*((([0-9A-Fa-f]{1,4}:){7}([0-9A-Fa-f]{1,4}|:))|(([0-9A-Fa-f]{1,4}:){6}(:[0-9A-Fa-f]{1,4}|((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){5}(((:[0-9A-Fa-f]{1,4}){1,2})|:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})|:))|(([0-9A-Fa-f]{1,4}:){4}(((:[0-9A-Fa-f]{1,4}){1,3})|((:[0-9A-Fa-f]{1,4})?:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){3}(((:[0-9A-Fa-f]{1,4}){1,4})|((:[0-9A-Fa-f]{1,4}){0,2}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){2}(((:[0-9A-Fa-f]{1,4}){1,5})|((:[0-9A-Fa-f]{1,4}){0,3}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(([0-9A-Fa-f]{1,4}:){1}(((:[0-9A-Fa-f]{1,4}){1,6})|((:[0-9A-Fa-f]{1,4}){0,4}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:))|(:(((:[0-9A-Fa-f]{1,4}){1,7})|((:[0-9A-Fa-f]{1,4}){0,5}:((25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(\.(25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3}))|:)))(%.+)?\s*$/,
            valid     = false,
            message;

        switch (true) {
            case (options.ipv4 && !options.ipv6):
                valid   = ipv4Regex.test(value);
                message = options.message || $.fn.Validator.i18n.ip.ipv4;
                break;

            case (!options.ipv4 && options.ipv6):
                valid   = ipv6Regex.test(value);
                message = options.message || $.fn.Validator.i18n.ip.ipv6;
                break;

            case (options.ipv4 && options.ipv6):
            /* falls through */
            default:
                valid   = ipv4Regex.test(value) || ipv6Regex.test(value);
                message = options.message || $.fn.Validator.i18n.ip['default'];
                break;
        }

        return {
            valid: valid,
            message: message
        };
    }
};

$.fn.Validator.validators.landline = {
    html5Attributes: {
        message: 'message',
        country: 'country'
    },

    // The supported countries
    COUNTRY_CODES: ['CN'],

    /**
     * Return true if the input value contains a valid landline number for the country
     * selected in the options
     *
     * - message: The invalid message
     * - country: The ISO-3166 country code. It can be
     *      - A country code
     *      - Name of field which its value defines the country code
     *      - Name of callback function that returns the country code
     *      - A callback function that returns the country code
     *
     * @returns {Boolean|Object}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        var country = options.country || this.COUNTRY_CODES[0];
        if (typeof country !== 'string' || $.inArray(country, this.COUNTRY_CODES) === -1) {
            // Try to determine the country
            country = validator.getDynamicOption($field, country);
        }

        if ($.inArray(country.toUpperCase(), this.COUNTRY_CODES) === -1) {
            return {
                valid: false,
                message: $.fn.Validator.helpers.format($.fn.Validator.i18n.landline.countryNotSupported, country)
            };
        }

        var isValid = true;
        switch (country.toUpperCase()) {
            // case 'CN':
            default:
                // Test: http://regexr.com/38mqi
                isValid = (/^(0\d{2,3}-?)?[2-9]\d{5,7}(-\d{1,5})?$/).test(value);
                break;
        }

        return {
            valid: isValid,
            message: $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.landline.country, $.fn.Validator.i18n.landline.countries[country])
        };
    }
};

$.fn.Validator.validators.length = {
    html5Attributes: {
        message: 'message',
        min: 'min',
        max: 'max',
        trim: 'trim',
        utf8bytes: 'utf8Bytes'
    },

    enableByHtml5: function($field) {
        var options   = {},
            maxLength = $field.attr('maxlength'),
            minLength = $field.attr('minlength');
        if (maxLength) {
            options.max = parseInt(maxLength, 10);
        }
        if (minLength) {
            options.min = parseInt(minLength, 10);
        }

        return $.isEmptyObject(options) ? false : options;
    },

    /**
     * Check if the length of element value is less or more than given number
     *
     * - min
     * - max
     * At least one of two keys is required
     * The min, max keys define the number which the field value compares to. min, max can be
     *      - A number
     *      - Name of field which its value defines the number
     *      - Name of callback function that returns the number
     *      - A callback function that returns the number
     *
     * - message: The invalid message
     * - trim: Indicate the length will be calculated after trimming the value or not. It is false, by default
     * - utf8bytes: Evaluate string length in UTF-8 bytes, default to false
     * @returns {Object}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (options.trim === true || options.trim === 'true') {
            value = $.trim(value);
        }

        if (value === '') {
            return true;
        }

        var min        = $.isNumeric(options.min) ? options.min : validator.getDynamicOption($field, options.min),
            max        = $.isNumeric(options.max) ? options.max : validator.getDynamicOption($field, options.max),
            // Credit to http://stackoverflow.com/a/23329386 (@lovasoa) for UTF-8 byte length code
            utf8Length = function(str) {
                var s = str.length;
                for (var i = str.length - 1; i >= 0; i--) {
                    var code = str.charCodeAt(i);
                    if (code > 0x7f && code <= 0x7ff) {
                        s++;
                    } else if (code > 0x7ff && code <= 0xffff) {
                        s += 2;
                    }
                    if (code >= 0xDC00 && code <= 0xDFFF) {
                        i--;
                    }
                 }
                 return s;
            },
            length     = options.utf8Bytes ? utf8Length(value) : value.length,
            isValid    = true,
            message    = options.message || $.fn.Validator.i18n.length['default'];

        if ((min && length < parseInt(min, 10)) || (max && length > parseInt(max, 10))) {
            isValid = false;
        }

        switch (true) {
            case (!!min && !!max):
                message = $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.length.between, [parseInt(min, 10), parseInt(max, 10)]);
                break;

            case (!!min):
                message = $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.length.more, parseInt(min, 10));
                break;

            case (!!max):
                message = $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.length.less, parseInt(max, 10));
                break;

            default:
                break;
        }

        return { valid: isValid, message: message };
    }
};

$.fn.Validator.validators.lt = {
    html5Attributes: {
        message: 'message',
        value: 'value',
        inclusive: 'inclusive'
    },

    enableByHtml5: function($field) {
        var type = $field.attr('type'),
            max  = $field.attr('max');
        if (max && type !== 'date') {
            return {
                value: max
            };
        }

        return false;
    },

    /**
     * Return true if the input value is less than or equal to given number
     *
     * - value: The number used to compare to. It can be
     *      - A number
     *      - Name of field which its value defines the number
     *      - Name of callback function that returns the number
     *      - A callback function that returns the number
     *
     * - inclusive [optional]: Can be true or false. Default is true
     * - message: The invalid message
     * @returns {Boolean|Object}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        value = this._format(value);

        var compareTo      = $.isNumeric(options.value) ? options.value : validator.getDynamicOption($field, options.value),
            compareToValue = this._format(compareTo);

        value = Number(value);
        return (options.inclusive === true || options.inclusive === undefined)
            ? {
                valid: value <= compareToValue,
                message: $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.lt['default'], compareTo)
            }
            : {
                valid: value < compareToValue,
                message: $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.lt.notInclusive, compareTo)
            };
    },

    _format: function(value) {
        return (value + '').replace(',', '.');
    }
};

$.fn.Validator.validators.mobile = {
    html5Attributes: {
        message: 'message',
        country: 'country'
    },

    // The supported countries
    COUNTRY_CODES: ['CN'],

    /**
     * Return true if the input value contains a valid mobile number for the country
     * selected in the options
     *
     * - message: The invalid message
     * - country: The ISO-3166 country code. It can be
     *      - A country code
     *      - Name of field which its value defines the country code
     *      - Name of callback function that returns the country code
     *      - A callback function that returns the country code
     *
     * @returns {Boolean|Object}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        var country = options.country || this.COUNTRY_CODES[0];
        if (typeof country !== 'string' || $.inArray(country, this.COUNTRY_CODES) === -1) {
            // Try to determine the country
            country = validator.getDynamicOption($field, country);
        }

        if ($.inArray(country.toUpperCase(), this.COUNTRY_CODES) === -1) {
            return {
                valid: false,
                message: $.fn.Validator.helpers.format($.fn.Validator.i18n.mobile.countryNotSupported, country)
            };
        }

        var isValid = true;
        switch (country.toUpperCase()) {
            case 'CN':
            default:
                // Test: http://regexr.com/38mqi
                isValid = (/^0?1[3-8]\d{9}$/).test(value);
                break;
        }

        return {
            valid: isValid,
            message: $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.mobile.country, $.fn.Validator.i18n.mobile.countries[country])
        };
    }
};

$.fn.Validator.validators.numeric = {
    html5Attributes: {
        message: 'message',
        separator: 'separator'
    },

    enableByHtml5: function($field) {
        return ('number' === $field.attr('type')) && ($field.attr('step') !== undefined) && ($field.attr('step') % 1 !== 0);
    },

    /**
     * Validate decimal number
     *
     * - message: The invalid message
     * - separator: The decimal separator. Can be "." (default), ","
     * @returns {Boolean}
     */
    validate: function(validator, $field, options) {
        if (this.enableByHtml5($field) && $field.get(0).validity && $field.get(0).validity.badInput === true) {
            return false;
        }

        var value = $field.val();
        if (value === '') {
            return true;
        }
        var separator = options.separator || '.';
        if (separator !== '.') {
            value = value.replace(separator, '.');
        }

        return !isNaN(parseFloat(value)) && isFinite(value);
    }
};

$.fn.Validator.validators.regexp = {
    html5Attributes: {
        message: 'message',
        regexp: 'regexp'
    },

    enableByHtml5: function($field) {
        var pattern = $field.attr('pattern');
        if (pattern) {
            return {
                regexp: pattern
            };
        }

        return false;
    },

    /**
     * Check if the element value matches given regular expression
     *
     * - regexp: The regular expression you need to check
     * @returns {Boolean}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        var regexp = ('string' === typeof options.regexp) ? new RegExp(options.regexp) : options.regexp;
        return regexp.test(value);
    }
};

$.fn.Validator.validators.remote = {
    html5Attributes: {
        message: 'message',
        name: 'name',
        type: 'type',
        url: 'url',
        data: 'data',
        delay: 'delay'
    },

    /**
     * Destroy the timer when destroying the Validator (using validator.destroy() method)
     */
    destroy: function(validator, $field, options) {
        if ($field.data('validate.remote.timer')) {
            clearTimeout($field.data('validate.remote.timer'));
            $field.removeData('validate.remote.timer');
        }
    },

    /**
     * Request a remote server to check the input value
     *
     * - url {String|Function}
     * - type {String} [optional] Can be GET or POST (default)
     * - data {Object|Function} [optional]: By default, it will take the value
     *  {
     *      <fieldName>: <fieldValue>
     *  }
     * - delay
     * - name {String} [optional]: Override the field name for the request.
     * - message: The invalid message
     * - headers: Additional headers
     * @returns {Deferred}
     */
    validate: function(validator, $field, options) {
        var value = $field.val(),
            dfd   = new $.Deferred();
        if (value === '') {
            dfd.resolve($field, 'remote', { valid: true });
            return dfd;
        }

        var name    = $field.attr('data-validate-field'),
            data    = options.data || {},
            url     = options.url,
            type    = options.type || 'GET',
            headers = options.headers || {};

        // Support dynamic data
        if ('function' === typeof data) {
            data = data.call(this, validator);
        }

        // Parse string data from HTML5 attribute
        if ('string' === typeof data) {
            data = JSON.parse(data);
        }

        // Support dynamic url
        if ('function' === typeof url) {
            url = url.call(this, validator);
        }

        data[options.name || name] = value;
        function runCallback() {
            var xhr = $.ajax({
                type: type,
                headers: headers,
                url: url,
                dataType: 'json',
                data: data
            });
            xhr.then(function(response) {
                response.valid = response.valid === true || response.valid === 'true';
                dfd.resolve($field, 'remote', response);
            });

            dfd.fail(function() {
                xhr.abort();
            });

            return dfd;
        }

        if (options.delay) {
            // Since the form might have multiple fields with the same name
            // I have to attach the timer to the field element
            if ($field.data('validate.remote.timer')) {
                clearTimeout($field.data('validate.remote.timer'));
            }

            $field.data('validate.remote.timer', setTimeout(runCallback, options.delay));
            return dfd;
        } else {
            return runCallback();
        }
    }
};

$.fn.Validator.validators.required = {
    enableByHtml5: function($field) {
        var required = $field.attr('required') + '';
        return ('required' === required || 'true' === required);
    },

    /**
     * Check if input value is empty or not
     *
     * @returns {Boolean}
     */
    validate: function(validator, $field, options) {
        var type = $field.attr('type');
        if ('radio' === type || 'checkbox' === type) {
            return validator
                        .getFieldElements($field.attr('data-validate-field'))
                        .filter(':checked')
                        .length > 0;
        }

        if ('number' === type && $field.get(0).validity && $field.get(0).validity.badInput === true) {
            return true;
        }

        return $.trim($field.val()) !== '';
    }
};

$.fn.Validator.validators.step = {
    html5Attributes: {
        message: 'message',
        base: 'baseValue',
        step: 'step'
    },

    /**
     * Return true if the input value is valid step one
     *
     * - baseValue: The base value
     * - step: The step
     * - message: The invalid message
     * @returns {Boolean|Object}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        options = $.extend({}, { baseValue: 0, step: 1 }, options);
        value   = parseFloat(value);
        if (!$.isNumeric(value)) {
            return false;
        }

        var round = function(x, precision) {
                var m = Math.pow(10, precision);
                x = x * m;
                var sign   = (x > 0) | -(x < 0),
                    isHalf = (x % 1 === 0.5 * sign);
                if (isHalf) {
                    return (Math.floor(x) + (sign > 0)) / m;
                } else {
                    return Math.round(x) / m;
                }
            },
            floatMod = function(x, y) {
                if (y === 0.0) {
                    return 1.0;
                }
                var dotX      = (x + '').split('.'),
                    dotY      = (y + '').split('.'),
                    precision = ((dotX.length === 1) ? 0 : dotX[1].length) + ((dotY.length === 1) ? 0 : dotY[1].length);
                return round(x - y * Math.floor(x / y), precision);
            };

        var mod = floatMod(value - options.baseValue, options.step);
        return {
            valid: mod === 0.0 || mod === options.step,
            message: $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.step['default'], [options.step])
        };
    }
};

$.fn.Validator.validators.tel = {
    html5Attributes: {
        message: 'message',
        country: 'country'
    },

    enableByHtml5: function($field) {
        return 'tel' === $field.attr('type');
    },

    // The supported countries
    COUNTRY_CODES: ['CN'],

    /**
     * Return true if the input value contains a valid telephone number for the country
     * selected in the options
     *
     * - message: The invalid message
     * - country: The ISO-3166 country code. It can be
     *      - A country code
     *      - Name of field which its value defines the country code
     *      - Name of callback function that returns the country code
     *      - A callback function that returns the country code
     *
     * @returns {Boolean|Object}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        var country = options.country || this.COUNTRY_CODES[0];
        if (typeof country !== 'string' || $.inArray(country, this.COUNTRY_CODES) === -1) {
            // Try to determine the country
            country = validator.getDynamicOption($field, country);
        }

        if ($.inArray(country.toUpperCase(), this.COUNTRY_CODES) === -1) {
            return {
                valid: false,
                message: $.fn.Validator.helpers.format($.fn.Validator.i18n.tel.countryNotSupported, country)
            };
        }

        var isValid = true;
        switch (country.toUpperCase()) {
            // case 'CN':
            default:
                // Test: http://regexr.com/38mqi
                isValid = (/^((00|\+)?(86(?:-| )))?((1[3-8]\d[- ]?\d{4}[- ]?\d{4})|((0\d{2,3}[- ]?)?([2-9]((\d{2,3}[- ]?\d{4})|(\d{3}[- ]?\d{3})))([- ]\d{1,4})?))$/).test(value);
                break;
        }

        return {
            valid: isValid,
            message: $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.tel.country, $.fn.Validator.i18n.tel.countries[country])
        };
    }
};

$.fn.Validator.validators.tocase = {
    html5Attributes: {
        message: 'message',
        'case': 'case'
    },

    /**
     * Check if a string is a lower or upper case one
     *
     * - message: The invalid message
     * - case: Can be 'lower' (default) or 'upper'
     * @returns {Object}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        var tocase = (options['case'] || 'lower').toLowerCase();
        return {
            valid: ('upper' === tocase) ? value === value.toUpperCase() : value === value.toLowerCase(),
            message: options.message || (('upper' === tocase) ? $.fn.Validator.i18n.tocase.upper : $.fn.Validator.i18n.tocase['default'])
        };
    }
};

$.fn.Validator.validators.url = {
    html5Attributes: {
        message: 'message',
        allowlocal: 'allowLocal',
        protocol: 'protocol'
    },

    enableByHtml5: function($field) {
        return ('url' === $field.attr('type'));
    },

    /**
     * Return true if the input value is a valid URL
     *
     * - message: The error message
     * - allowLocal: Allow the private and local network IP. Default to false
     * - protocol: The protocols, separated by a comma. Default to "http, https, ftp"
     * @returns {Boolean}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '') {
            return true;
        }

        // Credit to https://gist.github.com/dperini/729294
        //
        // Regular Expression for URL validation
        //
        // Author: Diego Perini
        // Updated: 2010/12/05
        //
        // the regular expression composed & commented
        // could be easily tweaked for RFC compliance,
        // it was expressly modified to fit & satisfy
        // these test for an URL shortener:
        //
        //   http://mathiasbynens.be/demo/url-regex
        //
        // Notes on possible differences from a standard/generic validation:
        //
        // - utf-8 char class take in consideration the full Unicode range
        // - TLDs are mandatory unless `allowLocal` is true
        // - protocols have been restricted to ftp, http and https only as requested
        //
        // Changes:
        //
        // - IP address dotted notation validation, range: 1.0.0.0 - 223.255.255.255
        //   first and last IP address of each class is considered invalid
        //   (since they are broadcast/network addresses)
        //
        // - Added exclusion of private, reserved and/or local networks ranges
        //   unless `allowLocal` is true
        //
        // - Added possibility of choosing a custom protocol
        //
        var allowLocal = options.allowLocal === true || options.allowLocal === 'true',
            protocol   = (options.protocol || 'http, https, ftp').split(',').join('|').replace(/\s/g, ''),
            urlExp     = new RegExp(
                "^" +
                // protocol identifier
                "(?:(?:" + protocol + ")://)" +
                // user:pass authentication
                "(?:\\S+(?::\\S*)?@)?" +
                "(?:" +
                // IP address exclusion
                // private & local networks
                (allowLocal
                    ? ''
                    : ("(?!(?:10|127)(?:\\.\\d{1,3}){3})" +
                       "(?!(?:169\\.254|192\\.168)(?:\\.\\d{1,3}){2})" +
                       "(?!172\\.(?:1[6-9]|2\\d|3[0-1])(?:\\.\\d{1,3}){2})")) +
                // IP address dotted notation octets
                // excludes loopback network 0.0.0.0
                // excludes reserved space >= 224.0.0.0
                // excludes network & broadcast addresses
                // (first & last IP address of each class)
                "(?:[1-9]\\d?|1\\d\\d|2[01]\\d|22[0-3])" +
                "(?:\\.(?:1?\\d{1,2}|2[0-4]\\d|25[0-5])){2}" +
                "(?:\\.(?:[1-9]\\d?|1\\d\\d|2[0-4]\\d|25[0-4]))" +
                "|" +
                // host name
                "(?:(?:[a-z\\u00a1-\\uffff0-9]+-?)*[a-z\\u00a1-\\uffff0-9]+)" +
                // domain name
                "(?:\\.(?:[a-z\\u00a1-\\uffff0-9]+-?)*[a-z\\u00a1-\\uffff0-9]+)*" +
                // TLD identifier
                "(?:\\.(?:[a-z\\u00a1-\\uffff]{2,}))" +
                // Allow intranet sites (no TLD) if `allowLocal` is true
                (allowLocal ? '?' : '') +
                ")" +
                // port number
                "(?::\\d{2,5})?" +
                // resource path
                "(?:/[^\\s]*)?" +
                "$", "i"
        );

        return urlExp.test(value);
    }
};

$.fn.Validator.validators.zip = {
    html5Attributes: {
        message: 'message',
        country: 'country'
    },

    COUNTRY_CODES: ['CN'],

    /**
     * Return true if and only if the input value is a valid country zip code
     *
     * - message: The invalid message
     * - country: The country
     *
     * The country can be defined by:
     * - An ISO 3166 country code
     * - Name of field which its value defines the country code
     * - Name of callback function that returns the country code
     * - A callback function that returns the country code
     *
     * callback: function(value, validator, $field) {
     *      // value is the value of field
     *      // validator is the Validator instance
     *      // $field is jQuery element representing the field
     * }
     *
     * @returns {Boolean|Object}
     */
    validate: function(validator, $field, options) {
        var value = $field.val();
        if (value === '' || !options.country) {
            return true;
        }

        var country = options.country;
        if (typeof country !== 'string' || $.inArray(country, this.COUNTRY_CODES) === -1) {
            // Try to determine the country
            country = validator.getDynamicOption($field, country);
        }

        if (!country || $.inArray(country.toUpperCase(), this.COUNTRY_CODES) === -1) {
            return { valid: false, message: $.fn.Validator.helpers.format($.fn.Validator.i18n.zip.countryNotSupported, country) };
        }

        var isValid = false;
        country = country.toUpperCase();
        switch (country) {
            // case 'CN':
            /* falls through */
            default:
                isValid = /^\d{6}$/.test(value);
                break;
        }

        return {
            valid: isValid,
            message: $.fn.Validator.helpers.format(options.message || $.fn.Validator.i18n.zip.country, $.fn.Validator.i18n.zip.countries[country])
        };
    }
};

$.fn.Validator.i18n = $.extend(true, $.fn.Validator.i18n, {
    alpha: {
        'default': '只能输入字母'
    },
    alphadigits: {
        'default': '只能输入字母或数字'
    },
    between: {
        'default': '请输入在 %s 和 %s 之间的数值',
        notInclusive: '请输入在 %s 和 %s 之间(不含两端)的数值'
    },
    callback: {
        'default': '请输入有效的值'
    },
    choice: {
        'default': '请输入有效的值',
        less: '请至少选中 %s 个选项',
        more: '最多只能选中 %s 个选项',
        between: '请选择 %s 至 %s 个选项'
    },
    cnchar: {
        'default': '只能输入中文字符'
    },
    color: {
        'default': '请输入有效的颜色值'
    },
    date: {
        'default': '请输入有效的日期',
        min: '请输入 %s 或之后的日期',
        max: '请输入 %s 或以前的日期',
        range: '请输入 %s 和 %s 之间的日期'
    },
    different: {
        'default': '请输入不同的值'
    },
    digits: {
        'default': '请输入有效的数字'
    },
    email: {
        'default': '请输入有效的email地址'
    },
    equalto: {
        'default': '请输入一样的值'
    },
    file: {
        'default': '请选择有效的文件'
    },
    gt: {
        'default': '请输入大于等于 %s 的数值',
        notInclusive: '请输入大于 %s 的数值'
    },
    hexColor: {
        'default': '请输入有效的16进制颜色值'
    },
    id: {
        'default': '请输入有效的身份证件号码',
        countryNotSupported: '不支持 %s 国家或地区',
        country: '请输入有效的 %s 国家或地区的身份证件号码',
        countries: {
            CN: ''
        }
    },
    integer: {
        'default': '请输入有效的整数值'
    },
    ip: {
        'default': '请输入有效的IP地址',
        ipv4: '请输入有效的IPv4地址',
        ipv6: '请输入有效的IPv6地址'
    },
    length: {
        'default': '请输入符合长度限制的值',
        less: '最多只能输入 %s 个字符',
        more: '需要输入至少 %s 个字符',
        between: '请输入 %s 至 %s 个字符'
    },
    lt: {
        'default': '请输入小于等于 %s 的数值',
        notInclusive: '请输入小于 %s 的数值'
    },
    landline: {
        'default': '请输入有效的固定电话号码',
        countryNotSupported: '不支持 %s 国家或地区',
        country: '请输入有效的%s国家或地区的固定电话号码',
        countries: {
            CN: ''
        }
    },
    mobile: {
        'default': '请输入有效的手机号码',
        countryNotSupported: '不支持该国家代码 %s',
        country: '请输入有效的%s手机号码',
        countries: {
            CN: ''
        }
    },
    numeric: {
        'default': '请输入有效的数值，允许小数'
    },
    regexp: {
        'default': '请输入符合正则表达式限制的值'
    },
    required: {
        'default': '请填写必填项目'
    },
    remote: {
        'default': '请输入有效的值'
    },
    step: {
        'default': '请输入在基础值上，增加 %s 的整数倍的数值'
    },
    tel: {
        'default': '请输入有效的手机/电话号码',
        countryNotSupported: '不支持该国家代码 %s',
        country: '请输入有效的%s手机/电话号码',
        countries: {
            CN: ''
        }
    },
    tocase: {
        'default': '只能输入小写字母',
        upper: '只能输入小写字母'
    },
    url: {
        'default': '请输入一个有效的URL地址'
    },
    zip: {
        'default': '请输入有效的邮政编码',
        countryNotSupported: '不支持%s国家或地区',
        country: '请输入有效的%s国家或地区的邮政编码',
        countries: {
            CN: '邮政编码'
        }
    }
});

}(window.jQuery));
