//自定义插件
$.fn.some = function(fn, thisArg) {
    return Array.prototype.slice.call(this).some(fn, thisArg);
};

$.fn.every = function(fn, thisArg) {
    return Array.prototype.slice.call(this).every(fn, thisArg);
};

// 自定义函数

//判断是否为dom元素
function isElement(dom) {
    if(dom && dom.length) {
        dom = dom[0];
    }
    return !!(dom && dom.nodeName && dom.nodeType === 1);
}

//=全选
function checkAll(el, elements) {
    $(elements).prop('checked', $(el).prop('checked'));
}

// 从DOM节点上获取配置项
function getDomOptions(element, prefix) {
    if(!prefix) throw new Error('请传入prefix参数');
    prefix = new RegExp('^' + prefix.toLowerCase());
    var ret = {},
        attrs = element && element.attributes,
        len = attrs && attrs.length,
        key,
        data;

    while (len--) {
        data = attrs[len];
        key = data.name;

        if (key.substring(0, 5) !== 'data-') {
            continue;
        }

        key = key.substring(5);
        data = parseData(data.value);

        data === undefined || (ret[key] = data);
    }

    return ret;
}

// Derive options from element data-attrs
function dataOptions(element, prefix){
    if(!prefix) throw new Error('dataOptions：请传入prefix参数');
    var data = $(element).data(),
        out = {}, inkey,
        replace = new RegExp('^' + prefix.toLowerCase() + '([A-Z])');
        // console.log(data);

    prefix = new RegExp('^' + prefix.toLowerCase());
    for (var key in data) {
        if (prefix.test(key)) {
            inkey = key.replace(replace, function(_, a){
                return a.toLowerCase();
            });
            out[inkey] = data[key];
        }
    }
    return out;
}

$.globalEval = function(data) {
    if (data && $.trim(data)) {
        (window.execScript || function(data) {
            window['eval'].call(window, data);
        })(data);
    }
};

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

/*通用货币格式化*/
var Currency = {
    spec: {
        "decimals":2,
        "dec_point":".",
        "thousands_sep":"",
        "sign":"\uffe5"
    },
    format: function(num, force) {
        var part;
        var sign = this.spec.sign || '';
        if (!(num || num === 0) || isNaN(+num)) return num;
        var num = parseFloat(num);
        if (this.spec.cur_rate) {
            num = num * this.spec.cur_rate;
        }
        num = Math.round(num * Math.pow(10, this.spec.decimals)) / Math.pow(10, this.spec.decimals) + '';
        var p = num.indexOf('.');
        if (p < 0) {
            p = num.length;
            part = '';
        } else {
            part = num.substr(p + 1);
        }
        while (part.length < this.spec.decimals) {
            part += '0';
        }
        var curr = [];
        while (p > 0) {
            if (p > 2) {
                p -= 3;
                curr.unshift(num.substr(p, 3));
            } else {
                curr.unshift(num.substr(0, p));
                break;
            }
        }
        if (!part) {
            this.spec.dec_point = '';
        }
        if (force) {
            sign = '<span class="price-currency">' + sign + '</span>';
        }
        return sign + curr.join(this.spec.thousands_sep) + this.spec.dec_point + part;
    },
    number: function(format) {
        if (!format) return null;
        if (isNaN(+format)) {
            if (format instanceof jQuery || (format.nodeName && format.nodeType === 1)) format = $(format).val() || $(format).text();
            if (format.indexOf(this.spec.sign) == 0) format = format.split(this.spec.sign)[1];
        }
        return +format;
    },
    calc: function(calc, n1, n2, noformat) {
        if (!(n1 || n1 === 0)) return null;
        if (!n2) {
            n1 = this.number(n1);
        }
        else {
            calc = !calc || calc == 'add' ? 1 : - 1;
            var t1 = 1,
            t2 = 1;
            if (n1 instanceof Array && n1.length) {
                t1 = n1[1];
                n1 = n1[0];
            }
            if (n2 instanceof Array && n2.length) {
                t2 = n2[1];
                n2 = n2[0];
            }
            var decimals = Math.pow(10, this.spec.decimals * this.spec.decimals);
            n1 = Math.abs(t1 * decimals * this.number(n1) + calc * t2 * decimals * this.number(n2)) / decimals;
        }
        if (!noformat) n1 = this.format(n1);
        return n1;
    },
    add: function(n1, n2, flag) {
        return this.calc('add', n1, n2, flag);
    },
    diff: function(n1, n2, flag) {
        return this.calc('diff', n1, n2, flag);
    }
};


// plugins

// zepto.cookie.js

// formplus.js

//点击更换验证码
function changeVerify(element, hasEvent) {
    $(element).each(function(index){
        var $el = $(this);
        var url;
        var img;
        if(this.tagName === 'IMG') {
            img = $(this);
            url = $el.attr('src');
        }
        else {
            img = $el.siblings('img');
            url = $el.attr('href');
        }
        if(hasEvent) $el.on('click', function() {
            changeCode(img, url);
        });
        else changeCode(img, url);
    });
}
function changeCode(img, url){
    url = url || img.attr('src');
    var random = +new Date;
    var urlrandom = url.match(/\b_=([\d]+)\b/);
    if(urlrandom) {
        url = url.replace(urlrandom[1], random);
    }
    else {
        url += (url.indexOf('?') > -1 ? '&' : '?') + '_=' + random;
    }
    img.attr('src', url);
    return false;
}

//=全选
function checkAll(el, elements) {
    $(elements).prop('checked', $(el).prop('checked'));
}

//= placeholder兼容性实现
//页面初始化时对所有input做初始化
//Placeholder.init();
//或者单独设置某个元素
//Placeholder.create($('t1'));
var Placeholder = {
    support: (function() {
        return 'placeholder' in document.createElement('input');
    })(),
    //提示文字的样式
    className: 'placeholder',
    init: function() {
        if (!this.support) {
            this.create($('input, textarea'));
        }
    },
    build: function(input, html) {
        var parent = input.parent();
        var $this = Placeholder;
        if(parent.css('position') == 'static') {
            parent.css('position', 'relative');
        }
        var placeholder = input.prev('.' + this.className) || $('<span>', {
            'class': this.className,
            html: html,
            style: 'visibility:hidden;'
        }).before(input)
        .locate({target: input, from:'lc', to:'lc', offset:{x:4}, offsetParent:true})
        .on('click', function(e){
            $this.hide(this);
            input.focus();
        });
        return placeholder;
    },
    create: function(inputs) {
        var $this = this;
        $(inputs).each(function(index){
            var el = $(this);
            if (!$this.support && el.attr('placeholder')) {
                var value = el.attr('placeholder');
                el.data('placeholder', $this.build(el, value));

                $this.show(el);

                el.on('focusin', function(e) {
                    $this.hide(this);
                }).on('focusout', function(e) {
                    $this.show(this);
                });
            }
        });
    },
    show: function(el) {
        if(!this.support && el.value === '' && $(el).is(':visible') && $(el).css('visibility') !== 'hidden') {
            $(el).data('placeholder').css('visibility', 'visible');
        }
    },
    hide: function(el) {
        if(!this.support) ($(el).data('placeholder') || $(el)).css('visibility', 'hidden');
    }
};

$(function(){
    var forms = document.forms;
    if(forms.length) {


        //= 自动绑定更换验证码
        changeVerify($('form .auto-change-verify-handle'), true);

        //= 记住帐号
        $('form .action-remember-account').on('change', function(e){
            if(this.checked) {
                $.cookie('S[SIGN][REMEMBER]', '1', 365);
            }
            else {
                $.cookie('S[SIGN][REMEMBER]', '0', 365);
            }
        }).trigger('change');

        //检测帐号重复
        $('form [data-remote]').on('change', function(e) {
            var element = $(this);
            var value = this.value;
            var name = this.name;
            var url = $(this).data('remote');
            var method = element.data('remoteType') || element.parents('form').attr('method');
            var callback = element.data('remoteCallback');
            if(url && name && value) {
                $[method](url, name + '=' + value, function(rs) {
                    if(typeof rs === 'string') {
                        rs = JSON.parse(rs);
                    }
                    if(callback) return callback.call(element, rs);
                    var tips = Tips.init({target: element.closest('.form-row')});
                    if(rs.error) {
                        return tips.error(rs.message);
                    }
                    else if(rs.success) {
                        return tips.success(rs.message);
                    }
                })
            }
        });
    }

    //处理 type=number 格式容错


    //处理input placeholder兼容
    Placeholder.init();
});




/*地区选择 */
var AreaWidget = function(options){
    var self = this;
    self.options = {
        dataUrl: 'data.json',
        select: null,
        level: 3,
        name: 'area[]',
        initData: null,
        initCallback: function() {}
    };
    var init = function(){
        self.options = $.extend(self.options,options);
        self.box = self.options.select;
        //get data
        $.ajax({
                type:"GET",
                url:self.options.dataUrl,
                dataType:"json",
                success:function(data){
                    if(data){
                        processData(data);
                    }else{
                        self.box.html('地区数据加载异常,请检查网络。');
                    }
                }
        });
    }
    var processData = function(data){
        // var selectBox = $('<span></span>').appendTo(self.box.empty()),
        //     inputBox = $('<span></span>').appendTo(self.box);
        self.box.empty();
        self.data = data;
        self.nowList = [];
        self.selectList = [];
        self.selected = [];

        for(var i=0;i<self.options.level;i++){
            if(i == 0){
                self.selectList[i] = $('<select></select>').appendTo(self.box).change(freshTheList);
            }else{
                self.selectList[i] = $('<select style="display:none;"></select>').appendTo(self.box).change(freshTheList);
            }
        }

        var tempHtml ='';
        $.each(self.data,function(i,item){
            tempHtml += "<option value='"+self.data[i].id+"'>"+self.data[i].value+"</option>";
        });
        self.selectList[0].html('<option value="">-请选择-</option>'+tempHtml);
        self.nowList[0] = self.data;

        if(self.options.initData){
            self.input = $('<input type="hidden" name="' + self.options.name + '" value="'+self.options.initData+'">').appendTo(self.box);
            var initData = self.options.initData.split(',');
            $.each(self.selectList,function(index,item){
                initData[index] && item.val(initData[index]).trigger('change');
            });
            self.options.initCallback.call(self);
        }else{
            self.input = $('<input type="hidden" name="' + self.options.name + '">').appendTo(self.box);
        }
    }

    var freshTheList = function(){
        var el = $(this),
            index = 0, // = el.find('option').index() - 1,
            level = el.index();
        el.find('option').each(function(i, opt){
            if(opt.selected) index = i - 1;
        });
        if(self.selected[level + 1]){
            for(var i = level+1;i < self.options.level;i++){
                self.selectList[i].hide();
                self.selected.pop();
            }
        }
        if(el.val()) {
            self.selected[level] = el.val();
        }
        else {
            delete self.selected[level];
        }
        self.input.val(self.selected.join(','));
        if(level+1 < self.options.level){
            self.nowList[level+1] = self.nowList[level][index]['children'];
            if(typeof(self.nowList[level+1]) === "undefined"){
                self.nowList[level+1] = null;
                self.selectList[level+1].html("<option> -- </option>");
                return 0;
            }else{
                var tempHtml = '';
                $.each(self.nowList[level+1], function(i,item){
                    tempHtml += "<option value='"+self.nowList[level+1][i].id+"'>"+self.nowList[level+1][i].value+"</option>";
                })
                self.selectList[level+1].show().html('<option value="">请选择</option>'+tempHtml);
            }
        }
    }
    init();
};

$.fn.multiSelect = function (options) {
    var instance = new AreaWidget($.extend({select: this}, options || {}));
    return $(this).data('multiSelectInstance', instance);
};
