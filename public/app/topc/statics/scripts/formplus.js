/*------- Author: Tyler Chao===tylerchao.sh@gmail.com -------*/

//=密码强度检测
var passwordStrength = function(value, key, className){
    //最小最大长度
    var minLength = 5;
    var maxLength = 20;

    //密码复杂度定义
    var lower = /[a-z]/g;
    var upper = /[A-Z]/g;
    var numberic = /\d/g;
    var symbols = /[\W_]/g;
    var repeat = new RegExp('(.{' + parseInt(value.length / 2) + ',})\1', 'g');

    //初始状态
    var status = 'poor';
    var strength = -1;

    if(!value || value.length < minLength) {
        strength = -1;
    }
    else {
        strength = parseInt(value.length / minLength) - 1;
    }
    if(value.match(repeat)) {
        strength --;
    }
    if(value.match(lower) || value.match(upper)) {
        strength ++;
    }
    if(value.match(numberic)) {
        strength ++;
    }
    if(value.match(symbols)) {
        strength ++;
    }
    if(value.length > minLength && strength < 2) {
        strength ++;
    }

    switch(strength) {
        case -1:
        case 0:
        case 1:
            status = 'poor';
            break;
        case 2:
            status = 'weak';
            break;
        case 3:
            status = 'good';
            break;
        default:
            status = 'strong';
            break;
    }
    key.className = (className ? className + ' ' : '') + 'password-' + status;
};
passwordStrength.init = function(element, key) {
    $(element).each(function(index, el){
        if(!el) return;
        if(key) {
            key = $(el.parentNode).find(key);
        }
        else {
            key = $(el).next();
        }
        if(!key) return;
        // key.css('visibility', 'visible');
        var className = key[0].className;
        $(el).on('input propertychange', function(e){
            if(e.type === 'propertychange' && e.propertyName.toLowerCase() !== 'value') return;
            var prev = passwordStrength.prev;
            var value = this.value;
            if(prev !== value) {
                passwordStrength(value, key[0], className);
            }
            passwordStrength.prev = value;
        });
    });
}

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

        //= 自动检测密码强度
        passwordStrength.init($('form .auto-password-check-handle'), '.password-check');

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
                    var tips = new Tips({target: element.closest('.form-row')});
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
