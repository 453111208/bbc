function isBody(element){
    return (/^(?:body|html)$/i).test(element.tagName);
}

function getCompatElement(){
    var doc = document;
    return (!doc.compatMode || doc.compatMode == 'CSS1Compat') ? doc.html : doc.body;
}

document.head = document.head || document.getElementsByTagName('head')[0];

//判断是否为dom元素
function isElement(dom) {
    dom = dom[0] || dom;
    return dom.nodeName && dom.nodeType === 1;
}

//简单html模板
function substitute(string, object) {
    return String(string).replace(/\\?\{([^{}]+)\}/g, function(match, name){
        if (match.charAt(0) === '\\') return match.slice(1);
        return (object[name] != null) ? object[name] : '';
    });
}

//=全选
function checkAll(el, elements) {
    $(elements).prop('checked', $(el).prop('checked'));
}

//outerHTML的兼容处理
$.fn.outerHtml = function() {
    if('outerHTML' in document.body) {
        var s = '';
        this.each(function(index) {
            s += this.outerHTML;
        });
        return s;
    }
    return $('<div>').append(this.clone(true, true)).html();
}

//获取元素内外边距或边框宽度
$.fn.patch = function (type) {
    var el = this;
    var args;
    if (type) {
        args = $.makeArray(type);
    }
    else {
        args = ['margin', 'padding', 'border'];
    }
    var _return = {
        x: 0,
        y: 0
    };

    $.each({x: ['left', 'right'], y: ['top', 'bottom']}, function(p1, p2) {
        $.each(p2, function(i, p) {
            try {
                $.each(args, function(i, arg) {
                    arg += '-' + p;
                    if (arg.indexOf('border') == 0) arg += '-width';
                    _return[p1] += parseInt(el.css(arg)) || 0;
                });
            } catch(e) {}
        });
    });
    return _return;
}

//locate the elements to the target
$.fn.locate = function(options){
    options = $.extend({
        relative: document.body,
        x: 'center', //left center right
        y: 'center' //top center bottom
    }, options);
    var el = $(options.relative), x, y, $this = $(this).css('position', 'absolute'),
        h = $this.height(), w = $this.width(), elH = (el.is('body') ? $(window) : el).innerHeight(), elW = el.innerWidth();

    switch(options.x) {
    case 0:
    case 'left':
        x = 0;
        break;
    case 'right':
        x = elW - w;
        break;
    default:
        x = parseInt((elW - w) / 2);
        break;
    }
    switch(options.y) {
    case 0:
    case 'top':
        y = 0;
        break;
    case 'bottom':
        y = elH - h;
        break;
    default:
        y = parseInt((elH - h) / 2);
        break;
    }

    $this.css({
        left: Math.max(0, Math.floor(x + (el.offset() ? el.offset().left : 0) + el.scrollLeft())),
        top: Math.max(0, Math.floor(y + (el.offset() ? el.offset().top : 0) + el.scrollTop()))
    });
    return this;
};


//Class的简单实现
var Class = function(o) {
    var F = function() {
        typeof this.init === 'function' && this.init.apply(this, arguments);
    };
    F.prototype = o || {};
    F.prototype.setOptions = function() {
        this.options = $.extend.apply(null, [true, {}, this.options].concat($.makeArray(arguments)));
        return this;
    };
    F.prototype.trigger = function(type, args) {
        type = this.options[type] || this.options['on' + type.replace(/^[a-z]/g, function(s) {
            return s.toUpperCase();
        })];
        typeof type === 'function' && type.apply(this, $.makeArray(args));
        return this;
    };
    return F;
};

(function() {
    // 用'log'替代打印调试
    var log, history, con = window.console;
    window.log = log = function() {
        history.push(arguments);
        con ? con.log[ con.firebug ? 'apply' : 'call'](con, Array.prototype.slice.call(arguments)) : alert(Array.prototype.slice.call(arguments).join('\n'));
    };
    log.history = history = [];
})();
