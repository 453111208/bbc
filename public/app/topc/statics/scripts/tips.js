var Tips = new Class({
    options: {
        // onShow: function () {},
        // onHide: function () {},
        form: 'inline',
        target: '.form-row',
        type: 'error',
        'class': 'caution'
    },
    /*setup: function() {
        this.options = $.extend.apply(null, [true, {}, this.options].concat($.makeArray(arguments)));
        return this;
    },*/
    init: function(options) {
        this.setOptions(options);
        this.target = $(this.options.target);
        return this.build();
    },
    build: function(options) {
        if(options) this.setOptions(options);
        options = this.options;
        var tag = options.form == 'inline' ? '<span>' : '<div>';
        this.element = this.target.data('tipInstance') || $(tag, {
            'style': 'display: none;',
            'class': 'icon-' + (options.type === 'success' ? 'checkmark-c' : 'alert') + ' ' + options['class']
        });
        this.target.data('tipInstance', this.element);
        this.element[options.where || 'appendTo'](this.target);
        this.trigger('load');
        return this;
    },
    show: function(msg, type, options) {
        options = options || {};
        if(type) options.type = type;
        if(!this.element) this.build(options);
        else {
            this.element.attr('class', this.element.attr('class').replace(/\b(icon-)[\w-]+\b/, '$1' + ((options.type || this.options.type) === 'success' ? 'checkmark-c' : 'alert')));
        }
        this.element.html(msg || '').css('display', '');
        this.trigger('show');
        return this;
    },
    hide: function(destroy) {
        this.element = this.target.data('tipInstance');
        if(!this.element) return;
        if(destroy) this.element.remove();
        else this.element.css('display', 'none');
        this.trigger('hide');
        return this;
    },
    success: function(msg, options) {
        return this.show(msg, 'success', options);
    },
    error: function(msg, options) {
        return this.show(msg, 'error', options);
    }
});

/*
//= 验证提示信息框
var formTips = new Class({
    options: {
        form: 'inline',
        type: 'error', // warn, error, notice, success
        'class': 'notice-inline',
        msg: '',
        target: document,
        where: null,
        single: false,
        store: false,
        destroy: false,
        position: ['ct', 'cb'],
        offset: [0,-9],
        intoView: true,
        autohide: 3
    },
    init: function(options) {
        this.setOptions(options);
        this.hidden = true;
        // this.toElement();
        return this;
    },
    toElement: function() {
        if(!this.element || !this.element.length) {
            var options = this.options;
            this.uid = $(options.target)[0].uniqueID;
            if(!this.uid) this.uid = $(options.target)[0].uniqueID = $.guid;
            var tag = options.form == 'inline' ? '<span>' : '<div>';
            var id = '_build_tips_' + (options.form ? options.form : '') + '_' + options.type + '_' + this.uid;
            this.element = options.single && document.getElementById(id) ? $('#' + id) : $(tag, {
                id: id,
                'class': 'caution '+ options.type + ' ' + options['class'],
                'style': 'display:none;',
                'html': '<span class="icon-' + (options.type === 'success' ? 'checkmark-c' : 'alert') + ' caution-content"></span>'
            });
            // this.element.inject(options.target, options.where);
            this.element[options.where || 'insertAfter'](options.target);
        }
        return this.element;
    },
    store: function (element) {
        element = $(element);
        if(!element.length) {
            element = $(this.options.target);
        }
        element.data('tips_instance', this);
        return this;
    },
    removeData: function (element) {
        element = $(element);
        if(!element.length) {
            element = $(this.options.target);
        }
        element.removeData('tips_instance');
        return this;
    },
    position: function(options) {
        if(!this.element) this.toElement();
        var position = {
            target: options.target,
            from: options.position instanceof 'array' ? options.position[0] : 'cb', //此元素定位基点 --为数值时类似offset
            to: options.position instanceof 'array' ? options.position[0] : options.position, //定位到目标元素的基点
            offset: options.offset // 偏移量
        }
        return this.element.locate(position);
    },
    show: function(msg, options) {
        if($.isPlainObject(msg)) {
            options = msg;
            msg = options.msg;
        }
        if(!this.hidden) return this;
        options = $.extend(this.options, options||{});
        if(!this.element) this.toElement();
        if(options.form && options.form != 'inline') this.element.locate(options);

        if(msg) this.element.find('.caution-content').html(msg);
        this.element.show();
        this.hidden = false;
        if(!isNaN(options.autohide) && options.autohide > 0) {
            clearTimeout(this.timer);
            this.timer = setTimeout(this.hide, options.autohide * 1000);
        }
        return this.options.store ? this.data(this.options.store) : this;
    },
    hide: function(destroy) {
        destroy = destroy || this.options.destroy;
        if(this.hidden) return this;
        if(!this.element) this.toElement();
        // if(this.element) {
        if(destroy !== false) {
            this.element.remove();
            this.element = null;
        }
        else this.element.hide();
        // }
        this.hidden = true;
        return this.removeData(this.options.store);
    }
});
formTips.tip = function(element, msg) {
    return new formTips({type:'notice', target: element || document, autohide: 0}).show(msg);
};
formTips.error = function(msg, element, options) {
    return new formTips({type: 'error', target: element || document, autohide: 0}).show(msg, options);
};
formTips.success = function(msg, element, options) {
    return new formTips({type:'success', target: element || document, autohide: 0}).show(msg, options);
};
formTips.warn = function(msg, element, options) {
    return new formTips({type: 'warn', target: element || document, where: 'perpend', autohide: 0, 'class': 'caution-inline', single: true, store: true}).show(msg, options);
};
*/
