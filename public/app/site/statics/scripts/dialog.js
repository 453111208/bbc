/*
 * jquery简单弹框组件
 * Author: Tyler Chao
 *
 * @param target 弹出框内容元素，接受url或jquery对象
 * @param id 弹出框的id
 * @param type 弹框类型:nohead,notitle,noclose,或模板字串
 * //@param template 页面中模板位置ID
 * @param width 弹出框宽度 0 或'auto'为不限制，支持传小数表示窗口的比例
 * @param height 弹出框高度 0 或'auto'为不限制，支持传小数表示窗口的比例
 * @param title 弹出框标题
 * @param load 载入时触发事件
 * @param show 显示时触发事件
 * @param close 关闭时触发事件
 * @param modal 是否在弹出时候其他区域不可操作
 * @param autoHide 是否在几秒后自动消失
 * @param locate 定位到哪里
 *      relative 相对定位的目标
 *      x 横向定位可以为0 left center right
 *      y 纵向定位可以为0 top center bottom
 *      offset 偏移目标位置
 *          x 左偏移
 *          y 右偏移
 * @param useIframeShim 是否使用iframe遮盖
 * @param async 异步调用方式: false, iframe, ajax
 * @param frameTpl iframe方式调用的模板
 * @param ajaxTpl ajax方式调用的模板
 * @param asyncOptions 异步请求的参数
 *      type 请求的方式
 *      data 请求的数据
 *      success 请求成功后执行
 *      error 请求失败后执行
 * @param component 弹出框的构成组件
 * @return this Dialog instance
 */

var Dialog = new Class({
    options: {
        title: '提示',
        /* id: null,
         * type: null,
         * width: 0,
         * height: 0,
         * modal: false,
         * autoHide: false,
         * load: function(){},
         * show: function(){},
         * close: function(){},
         */
        minHeight: 50,
        minWidth: 80,
        locate: {
            target: document.body,
            x: 'center',
            y: 'center'
        },
        /* useIframeShim: false,
         * async: false,*/
        frameTpl: '<iframe allowtransparency="allowtransparency" align="middle" frameborder="0" height="100%" width="100%" scrolling="auto" src="about:blank">请使用支持iframe框架的浏览器。</iframe>',
        ajaxTpl: '<div class="loading">loading...</div>',
        asyncOptions: {
            type: 'get'
            /* target: null,
             * data: '',
             */
        },
        component: {
            container: 'dialog',
            body: 'dialog-body',
            header: 'dialog-header',
            close: 'dialog-btn-close',
            content: 'dialog-content'
        }
    },
    init: function(target, options) {
        if (!target) return;
        this.doc = $(document.body);

        this.setOptions(options);
        options = this.options;

        var asyncOptions = options.asyncOptions || {};
        var container = this.container = this.build(target);
        this.body = container.find('.' + options.component.body);
        this.header = container.find('.' + options.component.header);
        this.title = this.header.find('h2');
        this.close = container.find('.' + options.component.close);
        this.content = container.find('.' + options.component.content);
        if(options.width && !isNaN(options.width)) {
            if(options.width <= 1 && options.width > 0) {
                options.width = options.width * $(window).innerWidth();
            }
            else {
                options.width = parseInt(options.width);
            }
        }
        if(options.height && !isNaN(options.height)) {
            if(options.height <= 1 && options.height > 0) {
                options.height = options.height * $(window).innerHeight();
            }
            else {
                options.height = parseInt(options.height);
            }
        }
        this.size = {
            x: options.width - this.body.patch().x - this.container.patch().x || '',
            y: options.height - this.body.patch().y - this.container.patch().x || ''
        };
        options.title || (this.header.find('h2')[0] && this.header.find('h2').remove());
        container.data('instance') || this.body.css({
            width: this.size.x,
            height: this.size.y
        });
        this.trigger('load', this);
        if ($.type(target) === 'string') {
            if (options.async === 'ajax' || options.async === true) {
                $.ajax($.extend({
                    url: target + '',
                    complete: $.proxy(function(xhr) {
                        this.content.html(xhr.responseText);
                        this.locate();
                        asyncOptions.callback.call(this, xhr);
                    }, this)
                }, asyncOptions));
            }
            else if(options.async === 'iframe'){
                var url = asyncOptions.data ? target + (target.indexOf('?') > 1 ? '&' : '?') + asyncOptions.data : target + '';
                this.content.find('iframe').attr('src', url).on('load', $.proxy(asyncOptions.success || function(){}, this));
            }
        }
        if (!!options.modal) {
            this.mask = new Mask(options.modal);
        }
        this.hidden = true;
        this.attach(); //执行初始化加载
    },
    attach: function() {
        this.show();
        //如果有存储实例，直接返回
        var self = this;
        if (!this.container.data('instance')) {
            this.container.data('instance', this).on('click', '.' + this.options.component.close, function(e) {
                self.hide();
            });
        }
        return this;
    },
    build: function(target) {
        var options = this.options;
        var single = $('#' + String(options.id));
        var main;

        if ($.type(target) === 'string') {
            if (options.async === 'ajax') {
                main = options.ajaxTpl;
            }
            else if(options.async === 'iframe') {
                main = options.frameTpl;
            }
            else {
                try {
                    main = convertTarget(target);
                } catch(e) {}
            }
        }
        else if(isElement(target)) {
            main = convertTarget(target);
        }

        if(single.length) {
            if(isElement(target)) single.data('instance').content.html(main);
            return single;
        }

        var template = '<div class="{container}" {id} data-module="dialog" tabindex="1">' + this.getTemplate() + '</div>';

        $.extend(options.component, {
            title: options.title,
            id: options.id ? 'id="' + options.id + '"' : '',
            main: main
        });

        function convertTarget(target) {
            target = $(target);
            return target.is('img,object,script') ? target.outerHtml() : target.html();
        }
        return $(substitute(template, options.component)).appendTo(this.doc);
    },
    getTemplate: function(type) {
        var options = this.options;
        type = type || options.type;
        var containerTpl = [
            '<div class="{body}">',
            '<div class="{header}">',
            '<h2>{title}</h2>',
            '<span><button type="button" class="{close}" title="关闭" hidefocus><i>×</i></button></span>',
            '</div>',
            '<div class="{content}">{main}</div>',
            '</div>'
        ];
        if (type === 'nohead') containerTpl[1] = containerTpl[2] = containerTpl[3] = containerTpl[4] = '';
        else if (type === 'notitle') containerTpl[2] = '';
        else if (type === 'noclose' || !!options.autoHide) containerTpl[3] = '';
        else if(typeof type === 'string') {
            return type;
        }

        return containerTpl.join('\n');
    },
    show: function() {
        var self = this;
        if(!this.hidden) return this;
        this.container.css('display', 'block');
        this.locate();

        if(this.options.useIframeShim) {
            $('<iframe/>', {
                frameborder: 0,
                src: 'about:blank',
                style: 'position:absolute;z-index:-1;border:0 none;filter:alpha(opacity=0);top:' + (-this.container.css('border-top-width')) + ';left:' + (-this.container.css('border-left-width')) + ';width:' + this.container.outerWidth() + 'px;height:' + this.container.outerHeight() + 'px;'
            }).appendTo(this.container);
        }

        this.container[0].focus();
        this.hidden = false;
        this.trigger('show', this);

        this.mask && this.mask.show();
        if(this.options.autoHide) {
            this.container.timer = setTimeout(function() {
                self.hide();
            }, parseInt(this.options.autoHide) * 1000);
        }

        return this;
    },
    hide: function() {
        if (this.hidden) return this;
        this.trigger('close', this);
        this.container.remove();
        this.hidden = true;
        this.hideMask();
        return this;
    },
    stopTimer: function(){
        if (this.container.timer) {
            clearTimeout(this.container.timer);
            this.container.timer = null;
        }
        return this;
    },
    hideMask: function () {
        this.mask && this.mask.hide();
        return this;
    },
    locate: function(options){
        options = options || this.options.locate;
        var element;
        if (this.size.y) element = this.container;
        else if(this.container.height() >= $(window).height()) element = $(window);
        if(element && isElement(element) && this.options.height) this.setHeight(element);
        this.container.locate(options);
        return this;
    },
    setHeight: function(el) {
        el = el || this.container;
        this.content.height(el.height() - this.container.patch().y - this.body.patch().y - this.header.outerHeight() - this.content.patch().y);
    }
});

//Mask
var Mask = new Class({
    options: {
        'class': 'mask'
        /*target: document.body,
        width: 0,
        height: 0,
        zIndex: null,
        locate: false,
        resize: false*/
    },
    init: function(options) {
        this.target = $(this.options.target || document.body);
        this.setOptions(options);

        this.element = $('<div>', {
            'data-module': 'mask',
            'class': this.options['class']
        }).appendTo(this.target);
        this.hidden = true;
    },
    setSize: function() {
        if(!this.element.is(':visible') || this.element.css('position') === 'fixed') return;
        this.element.css({
            width: this.options.width || this.target.width(),
            height: this.options.height || this.target.height()
        });
    },
    locate: function() {
        this.element.locate();
    },
    show: function() {
        if (!this.hidden) return;
        if (this.options.resize) $(window).resize($.proxy(this.setSize, this));
        this.setSize();

        this.element.css('display','block');
        if(this.options.zIndex) this.element.css('z-index', this.options.zIndex);
        if(this.options.locate) this.locate();
        this.trigger('show', this);
        this.hidden = false;
        return this;
    },
    hide: function() {
        if (this.hidden) return;
        if (this.options.resize) $(window).off('resize', $.proxy(this.setSize, this));

        this.element.remove();
        this.trigger('hide', this);
        this.hidden = true;
        return this;
    },
    toggle: function() {
        return this[this.hidden ? 'show' : 'hide']();
    }
});

$.dialog = function(target,options) {
    return new Dialog(target, options);
};

$.fn.dialog = function(options) {
    var dialog = new Dialog(this, options);
    return this.data('dialogInstance', dialog);
};

$.dialog.alert = function(msg, options) {
    new Dialog($('<div>' + msg + '<div class="actions"><button class="btn btn-simple dialog-btn-close"><span><span>确定</span></span></button></div></div>'), $.extend({
        width: 330,
        modal: true
    }, options));
};

$.dialog.confirm = function(msg, fn, options) {
    new Dialog($('<div>' + msg + '<div class="actions"><button class="btn btn-simple dialog-btn-close" data-return="1"><span><span>确定</span></span></button><button class="btn btn-simple dialog-btn-close" data-return="0"><span><span>取消</span></span></button></div></div>'), $.extend({
        width: 330,
        modal: true,
        show: function(e) {
            var self= this;
            this.close.on('click', function(){
                var val = parseInt($(this).attr('data-return'), 10);
                if(val && $.type(fn) === 'function') {
                    fn.call(self);
                }
            });
        }
    }, options));
};

$.dialog.image = function(url, options) {
    new Dialog('<img class="image-load-container dialog-btn-close" src="' + url + '">', $.extend({
        type: '<div class="{body}">{main}</div>',
        load: function() {
            var self = this;
            var img = $('.image-load-container');
            img.load(function(){
                if(this.width >= $(window).width()) {
                    $(this).width($(window).width() - self.container.patch().x - self.body.patch().x);
                }
                self.locate();
            });
        }
    }, options));
};
$.dialog.ajax = function(url, options) {
    new Dialog(url, $.extend({
        async: 'ajax',
        asyncOptions: {
            type: options.type || 'get'
        }
    }, options));
};
$.dialog.iframe = function(url, options) {
    new Dialog(url, $.extend({
        width: 600,
        useIframeShim: false,
        async: 'iframe'
    }, options));
};

$(function () {
    var element = $('[data-toggle=dialog]');
    var target = $(element.data('target') || element.attr('href'));
    var options = dataOptions(element, 'dialog');
    if(element.length && target.length) {
        element.on('click', function(event) {
            event.preventDefault();
            target.dialog(options);
        });
    }
});

//Message box
function Message(msg, type, delay, callback) {
    if(!msg) return null;
    if(!isNaN(type)) {
        delay = type;
        type = 'show';
    }
    else if($.type(delay) === 'function') {
        callback = delay;
        delay = 3;
    }
    else {
        type = type || 'show';
        delay = !isNaN(delay) ? delay : 3;
    }
    var icon = {
        'show': '',
        'success': '&#x25;',
        'error': '&#x21;'
    };
    var component = {
        container: 'message-' + type,
        body: 'message-body',
        content: 'message-content',
        icon: icon[type]
    };
    var pop = $('#pop_message_' + type);
    var instance;
    if(pop) {
        instance = pop.data('instance');
        if(instance) {
            instance.content.html(msg).show();
        }
    }
    new Dialog($('<div>' + msg + '</div>'), {
        id: 'pop_message_' + type,
        type: 'nohead',
        template: $('#message_template'),
        modal: false,
        single: true,
        autoHide: delay,
        component: component,
        show: function() {
            this.stopTimer();
        },
        close: $.type(callback) === 'function' ? $.proxy(callback, this) : null
    });
}

Message.show = function(msg, delay, callback) {
    Message(msg, 'show', delay, callback);
};
Message.hide = function(type) {
    type = type || 'show';
    try {
        $('#pop_message_' + type).data('instance').hide();
    } catch(e) {}
};
Message.error = function(msg, delay, callback) {
    Message(msg, 'error', delay, callback);
    return false;
};
Message.success = function(msg, delay, callback) {
    Message(msg, 'success', delay, callback);
    return true;
};
