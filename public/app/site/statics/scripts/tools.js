//获取<head>的兼容性写法
if(!document.head) {
    document.head = document.getElementsByTagName('head')[0];
}

//判断是否为dom元素
function isElement(dom) {
    if(dom instanceof jQuery) {
        dom = dom[0];
    }
    if(!dom) return false;
    return dom.nodeName && dom.nodeType === 1;
}

//以json直接量替换字符串中的 {xx} 部分，可看做一个简单的html模板
function substitute(string, object) {
    return String(string).replace(/\\?\{([^{}]+)\}/g, function(match, name){
        if (match.charAt(0) === '\\') return match.slice(1);
        return (object[name] != null) ? object[name] : '';
    });
}

//解析文本中js代码，并立即执行
function evalScripts(string, content, execScript){
    if(!string) return;
    var scripts = '';
    var text = string.replace(/<script[^>]*>([\s\S]*?)<\/script>/gi, function(){
        scripts += arguments[1] + '\n';
        return '';
    });
    $(content).html(execScript ? text : string);
    execScript && $.globalEval(scripts);
    return text;
}

//获取页面元素最大的z-index
function maxZindex(scope, increase) {
    scope = scope || 'div';
    scope = $(scope);
    var max = 0;
    if(scope.length) {
        var pos = scope.filter(function(el){
            if(!isElement(el) || ['script', 'link', 'base', 'style'].indexOf(el.tagName.toLowerCase()) > -1) return;
            return ['absolute','relative','fixed'].indexOf($(el).css('position')) > -1;
        });
        if(pos.length) {
            for(var i=0, j=pos.length;i<j;i++) {
                var z = pos[i].css('z-index');
                max = Math.max(max, isNaN(z) ? 0 : z);
            }
        }
    }
    if(increase) max += parseInt(increase);
    return Math.min(max, 2147483647);
}

// Derive options from element data-attrs
function dataOptions(element, prefix){
    if(!prefix) throw new Error('dataOptions：请传入prefix参数');
    var data = $(element).data(),
        out = {}, inkey,
        replace = new RegExp('^' + prefix.toLowerCase() + '([A-Z])');

    prefix = new RegExp('^' + prefix.toLowerCase());
    for (var key in data) {
        if (prefix.test(key)){
            inkey = key.replace(replace, function(_, a){
                return a.toLowerCase();
            });
            out[inkey] = data[key];
        }
    }
    return out;
}


// 通用倒计时，包括倒计时所在容器，倒数秒数，显示方式，回调。
function countdown(element, options){
    var self = this;
    options = $.extend({
        start: 60,
        secondOnly: false,
        callback: null
    }, options || {});
    var t = options.start;
    var sec = options.secondOnly;
    var fn = options.callback;
    var d = +new Date();
    var diff = Math.round((d + t * 1000) / 1000);
    this.timer = timeout(element, diff, fn);
    this.stop = function() {
        clearTimeout(self.timer);
    };

    function timeout(element, until, fn) {
        var str = '',
            started = false,
            left = {d: 0, h: 0, m: 0, s: 0, t: 0},
            current = Math.round(+new Date() / 1000),
            data = {d: '天', h: '时', m: '分', s: '秒'};

        left.s = until - current;

        if (left.s < 0) {
            return;
        }
        else if(left.s == 0) {
            fn && fn();
        }
        if(!sec) {
            if (Math.floor(left.s / 86400) > 0) {
              left.d = Math.floor(left.s / 86400);
              left.s = left.s % 86400;
              str += left.d + data.d;
              started = true;
            }
            if (Math.floor(left.s / 3600) > 0) {
              left.h = Math.floor(left.s / 3600);
              left.s = left.s % 3600;
              started = true;
            }
        }
        if (started) {
          str += ' ' + left.h + data.h;
          started = true;
        }
        if(!sec) {
            if (Math.floor(left.s / 60) > 0) {
              left.m = Math.floor(left.s / 60);
              left.s = left.s % 60;
              started = true;
            }
        }
        if (started) {
          str += ' ' + left.m + data.m;
          started = true;
        }
        if (Math.floor(left.s) > 0) {
          started = true;
        }
        if (started) {
          str += ' ' + left.s + data.s;
          started = true;
        }

        $(element).html(str);
        return setTimeout(function() {timeout(element, until,fn);}, 1000);
    }
}

//outerHTML的兼容处理
$.fn.outerHtml = function() {
    if('outerHTML' in document.body) {
        var s = '';
        this.each(function(index, el) {
            s += el.outerHTML;
        });
        return s;
    }
    return $('<div>').append(this.clone(true, true)).html();
};

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
};

//双dom9点定位
$.fn.locate = function(options){
    options = $.extend({
        relative: document.body,
        x: 'center', //left center right
        y: 'center'/*, //top center bottom
        offset: {   //偏移量
            x: 0,
            y: 0
        }*/
    }, options);
    var left,
        top,
        x,
        y,
        offset = options.offset,
        el = $(options.relative),
        $this = $(this).css('position', 'absolute'),
        h = $this.height(),
        w = $this.width(),
        elH = (el.is('body') ? $(window) : el).innerHeight(),
        elW = el.innerWidth();

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

    left = Math.max(0, Math.floor(x + (el.offset() ? el.offset().left : 0) + el.scrollLeft()));
    top = Math.max(0, Math.floor(y + (el.offset() ? el.offset().top : 0) + el.scrollTop()));

    if(typeof offset === 'string') {
        if(offset == 'top') {
            top -= h;
        }
        else if(offset == 'left') {
            left -= w;
        }
        else if(offset == 'bottom') {
            top += elH;
        }
        else if(offset == 'right') {
            left += elW;
        }
    }
    else if($.isPlainObject(offset)) {
        left += offset.x || 0;
        top += offset.y || 0;
    }

    $this.css({
        left: left,
        top: top
    });
    return this;
};

// 为ie8-做兼容
if (!Array.prototype.some) {
    Array.prototype.some = function(fn, thisArg) {
        var i = 0, n = this.length >>> 0;
        for (; i < n; i++) {
            if (i in this && fn.call(thisArg, this[i], i, this)) {
                return true;
            }
        }
        return false;
    };
}

if (!Array.prototype.every) {
    Array.prototype.every = function(fn, thisArg) {
        var i = 0, n = this.length >>> 0;
        for (; i < n; i++) {
            if (i in this && !fn.call(thisArg, this[i], i, this)) {
                return false;
            }
        }
        return true;
    };
}

if (!Array.prototype.indexOf) {
    Array.prototype.indexOf = function (searchElement, fromIndex) {
        var length = this.length >>> 0; // Hack to convert object.length to a UInt32
        fromIndex = +fromIndex || 0;

        if (Math.abs(fromIndex) === Infinity) {
            fromIndex = 0;
        }
        if (fromIndex < 0) {
            fromIndex += length;
            if (fromIndex < 0) {
                fromIndex = 0;
            }
        }

        for (; fromIndex < length; fromIndex++) {
            if (this[fromIndex] === searchElement) {
                return fromIndex;
            }
        }

        return -1;
    };
}

$.fn.some = function(fn, thisArg) {
    return Array.prototype.slice.call(this).some(fn, thisArg);
};

$.fn.every = function(fn, thisArg) {
    return Array.prototype.slice.call(this).every(fn, thisArg);
};

//Class的简单实现
var Class = function(o, props) {
    if (typeof o === 'function') o = {init: o};
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
    if(typeof props === 'object') {
        for(var prop in props) {
            if(props.hasOwnProperty(prop)) {
                F[prop] = props[prop];
            }
        }
    }
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
