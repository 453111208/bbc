/*------- Author: Tyler Chao===tylerchao.sh (at) gmail.com -------*/

//＝基本表单验证
var validatorMap = {
    'required': ['此项必填', function(element, v, type) {
        if (type == 'select') {
            var index = element.selectedIndex;
            v = element.options[index].value;
            return index >= 0 && (v != '' && v != '_NULL_');
        }
        return v !== null && v.length !== 0;
        // return v !== null && v != '' && v != '_NULL_';
    }],
    'minlength': [function(element, v, props) {
        if ($.type(props) != 'null') {
            return '输入不正确，至少' + props + '个字符';
        }
        return '';
    }, function(element, v, type, parent, props) {
        return v === null || v === '' || ($.type(props) != 'null' ? v.length >= +props : true);
    }],
    'maxlength': [function(element, v, props) {
        if ($.type(props) != 'null') {
            return '输入不正确，最多' + props + '个字符';
        }
        return '';
    }, function(element, v, type, parent, props) {
        return v === null || v === '' || ($.type(props) != 'null' ? v.length <= +props : true);
    }],
    'number': ['请填写数值', function(element, v) {
        return ! isNaN(v) && ! /^\s+$/.test(v);
    }],
    'digits': ['只能填写数字', function(element, v) {
        return ! /[^\d]/.test(v);
    }],
    'posint': ['请填写正整数', function(element, v) {
        return (!/[^\d]/.test(v) && v > 0);
    }],
    'natural': ['请填写大于等于0的整数', function(element, v) {
        return (!/[^\d]/.test(v) && v >= 0);
    }],
    'positive': ['请填写大于0的数值', function(element, v) {
        return (!isNaN(v) && ! /^\s+$/.test(v) && v > 0);
    }],
    'nonneg': ['请填写大于等于0的数值', function(element, v) {
        return (!isNaN(v) && ! /^\s+$/.test(v) && v >= 0);
    }],
    'alpha': ['只能填写英文字母', function(element, v) {
        return v === null || v == '' || /^[a-zA-Z]+$/.test(v);
    }],
    'alphanumber': ['请填写英文字母或者数字', function(element, v) {
        return ! /\W/.test(v) || /^[a-zA-Z0-9]+$/.test(v);
    }],
    'alphanumcn': ['请填写英文字母、中文或数字', function(element, v) {
        return ! /\W/.test(v) || /^[\u4e00-\u9fa5a-zA-Z0-9]+$/.test(v);
    }],
    'uncnchar': ['不能填写中文字符', function(element, v) {
        return ! /\W/.test(v) || ! /^[\u4e00-\u9fa5]+$/.test(v);
    }],
    'date': ['请填写日期，格式yyyy-mm-dd', function(element, v) {
        return v === null || v == '' || /^(?:19|20)[0-9]{2}[\/-](?:[1-9]|0[1-9]|1[012])[\/-](?:[1-9]|0[1-9]|[12][0-9]|3[01])$/.test(v);
    }],
    'email': ['请填写正确的email地址', function(element, v) {
        return v === null || v == '' || /^[a-z\d][a-z\d_.]*@[\w-]+(?:\.[a-z]{2,})+$/i.test(v);
    }],
    'emaillist': ['请填写正确的email地址，以","或";"分隔', function(element, v) {
        return v === null || v == '' || /^(?:[a-z\d][a-z\d_.]*@[\w-]+(?:\.[a-z]{2,})+[,;\s]?)+$/i.test(v);
    }],
    'mobile': ['请填写正确的手机号码', function(element, v) {
        return v === null || v == '' || /^0?1[34578]\d{9}$/.test(v);
    }],
    'landline': ['请填写正确的座机号码', function(element, v) {
        return v === null || v == '' || /^(0\d{2,3}-?)?[2-9]\d{5,7}(-\d{1,5})?$/.test(v);
    }],
    'tel': ['请填写正确的固话或手机号码', function(element, v) {
        return v === null || v == '' || /^0?1[3458]\d{9}$|^(0\d{2,3}-?)?[2-9]\d{5,7}(-\d{1,5})?$/.test(v);
    }],
    'zip': ['请填写正确的邮编', function(element, v) {
        return v === null || v == '' || /^\d{6}$/.test(v);
    }],
    'url': ['请填写正确的网址', function(element, v) {
        var pattern = "^((https|http|ftp|rtsp|mms)?://)"
        + "?(([0-9a-z_!~*'().&=+$%-]+: )?[0-9a-z_!~*'().&=+$%-]+@)?" //ftp的user@
        + "(([0-9]{1,3}\.){3}[0-9]{1,3}" // IP
        + "|" // 允许IP和域名
        + "([0-9a-z_!~*'()-]+\.)*" // 主机名 www.
        + "([0-9a-z][0-9a-z-]{0,61})?[0-9a-z]\." // 二级域名
        + "[a-z]{2,6})" // 顶级域名 .com or .museum
        + "(:[0-9]{1,4})?" // 端口
        + "((/?)|" // 最后的/非必需
        + "(/[0-9a-z_!~*'().;?:@&=+$,%#-]+)+/?)$"; //查询参数
        var re = new RegExp(pattern, 'i');
        return v === null || v == '' || re.test(v);
    }],
    'area': ['请选择完整的地区', function(element, v) {
        return $(element).find('select').every(function(sel) {
            var selValue = sel.value;
            if(sel.offsetHeight + sel.offsetWidth === 0) return true;
            return selValue != '' && selValue != '_NULL_';
        });
    }],
    'equalto': ['两次填写不一致', function(element, v, type, parent, props){
        var sibling = $(parent).find('[name="' + props + '"]');
        return !sibling[0] || v === sibling.val();
    }],
    'oneoftwo': ['请至少填写一项', function(element, v, type, parent, props) {
        var sibling = $(parent).find('[name="' + props + '"]');
        return !sibling[0] || v || sibling.val();
    }],
    'onerequired': ['请至少选择一项', function(element, v, type, parent) {
        var name = element.name;
        return $(parent).find('input' + (name ? '[name="' + name + '"]' : '')).some(function(el) {
            if ($.inArray(['checkbox', 'radio'], el.type)) return el.checked;
            return !!el.value;
        });
    }]
};

//= 统一表单验证入口
var validation = function(container, options) {
    if(typeof options === 'string') {
        options = {
            range: options
        };
    }
    options = $.extend({
        group: '.form-act',
        tips: {
            event: 'tap',
            arrow: true,
            placement: 'left bottom'
        }
    }, options || {});

    var ATTR = 'data-validate';
    var AllAttr = '[' + ATTR + '], [pattern], [type=number], [type=email], [type=tel], [type=url], [type=date], [required], [minlength], [maxlength], [data-equalto], [data-oneoftwo]';

    container = container ? $(container) : null;
    if (!container) return true;

    var config = container.data('validateConfig');
    var dataopts = dataOptions(container, 'validate');
    if(typeof config === 'string') {
        config = JSON.parse(config);
    }
    options = $.extend(options, config || {}, dataopts);

    if(container.is('form')) container.attr('novalidate', 'novalidate');
    var formElements = (container.is('form') || options.range === 'all') ? container.find(AllAttr) : $(container);
    var errElements = [];
    formElements.every(function(el, i) {
        var element = $(el);
        if(element.css('display') === 'none' || el.offsetHeight + el.offsetWidth === 0) return true;

        var vtypes = [];

        if(element.attr(ATTR)) {
            vtypes = element.attr(ATTR).split(' ');
        }
        if (element.attr('required')) {
            vtypes.unshift('required');
            // element.removeAttr('required');
        }

        var msg = element.attr('data-caution') || '';
        var pattern = element.attr('pattern');
        var re = new RegExp('^(?:' + pattern + ')$');
        var ckey = 'custom_' + $.uuid ++;
        if(msg) {
            msg = msg.split(/&&|&amp;&amp;/);
        }
        else {
            msg = [];
        }
        if(pattern) {
            if(!validatorMap[ckey]) {
                validatorMap[ckey] = [element.attr('data-attention') || '', function(element, v){
                    return v === null || v === '' || re.test(v);
                }];
            }
            vtypes.push(ckey);
        }

        $.each(['number', 'url', 'email', 'tel', 'date'], function(i, type) {
            if(element.attr('type') === type) {
                vtypes.push(type);
            }
        });

        $.each(['minlength', 'maxlength', 'data-equalto', 'data-oneoftwo'], function(i, type) {
            if(element.attr(type)) {
                vtypes.push(type);
            }
        });

        if (!vtypes.length) return true;

        var flag = false;
        for(var k = 0, l = vtypes.length; k < l; k ++) {
            var key = vtypes[k];
            var props = element.attr(key);
            key = key.replace(/^data-/, '');
            var validator = validatorMap[key];
            if (!validator) {
                flag = true;
                continue;
            }
            validator = validator instanceof Array ? validator : ['', validator];
            var feedback = element.closest(options.group);
            var caution = {
                // tips: new gmu.Popover(feedback, options.tips),
                msg: msg[k] || (typeof(validator[0]) === 'function' ? validator[0](element[0], element.val(), props) : validator[0])
            };
            validator = validator[1] || function(){return true};

            if (validator(element[0], element.val(), element.attr('type'), (container.is(AllAttr)) ? options.parent || container.parents('form')[0] || container.parent()[0] : container[0], props)) {
                // caution.tips.hide();
                feedback.removeClass('has-error');
                flag = true;
                continue;
            } else {
                //暂时关验证
                // flag = true;
                // break;

                if (caution.msg) {
                    // caution.tips.setContent(caution.msg).show();
                    alert(caution.msg);
                    feedback.addClass('has-error');
                }
                flag = false;
                break;
            }

        };
        if(!flag) {
            errElements.push(element);
            return false;
        }
        return true;
    });

    if(errElements.length){
        errElements.shift().focus();
        return false;
    }
    return true;
};

$.fn.validation = function (options) {
    return validation(this, options);
};

(function() {
    var DISABLED = 'disabled';

    var Sync = function(target, options) {
        var sponsor = $(target),
            dataForm,
            inject,
            tipElem,
            response = {},
            _getOptions = function(sponsor, options) {
                options = options || {};
                var _options = {},
                    opt,
                    isSubmit = sponsor.attr('type') === 'submit',
                    dataopts = dataOptions(sponsor, 'ajax');

                if(typeof dataopts.config === 'string') {
                    try {
                        _options = JSON.parse(dataopts.config) || {};
                        delete dataopts.config;
                    } catch(e) {
                        // _options = {};
                    }
                }
                else if($.isPlainObject(dataopts.config)) {
                    _options = dataopts.config;
                    delete dataopts.config;
                }

                if (isSubmit) dataForm = sponsor.parents('form');
                if (dataForm && dataForm[0]) opt = {
                    url: dataForm[0].action,
                    data: dataForm.serialize(),
                    type: dataForm[0].method || 'post'
                };
                else opt = {
                    url: sponsor.attr('href'),
                    type: dataopts.type || 'get'
                };
                var target = sponsor.attr('target');
                if(target && ['_self', '_blank', '_parent', '_top'].indexOf(target) < 0) {
                    opt.addition = target;
                }

                _options = $.extend(true, opt, options, _options, dataopts);

                if(isElement(_options.data)) {
                    _options.data = $(_options.data).serialize();
                }
                if(typeof _options.data === 'string') {
                    _options.data += '&response_json=true';
                }
                else if($.isPlainObject(_options.data)) {
                    _options.data.response_json = true;
                }
                return _options;
            },
            _defaultState = function(sponsor) {
                sponsor.removeClass(options.disabled);
            },
            _setCache = function(sponsor, value) {
                sponsor.data('ajaxCache', value);
            },
            _getCache = function(sponsor) {
                return sponsor.data('ajaxCache');
            },
            _clearCache = function(sponsor) {
                sponsor.removeData('ajaxCache');
            },
            _progressCache = function(sponsor) {
                var cache = _getCache(sponsor);
                if(!options.syncCache || !cache) return false;
                cache.success(response.data);
                return true;
            },
            _jsonSuccess = function(text) {
                if(typeof text === 'string') {
                    text = JSON.parse(text);
                }
                response.json = text;
                _onSuccess(text);
            },
            _onSuccess = function(text) {
                _defaultState(sponsor);
                if(options.syncCache && sponsor[0]) _setCache(sponsor, options);
                if (response.json) _progress(text);
                else {
                    typeof options.callback === 'function' && options.callback(null, sponsor);
                    location.href = options.url + (/\?/.test(options.url) ? '&' : '?') + options.data;
                }
            },
            _progress = function(rs) {
                if (!rs) return;
                if (options.progress) return options.progress.call(null, rs, sponsor);
                var redirect = rs.redirect;
                var msg;
                var Message = new gmu.Message();

                if (!options.showMessage || ['error', 'success'].every(function(v) {
                    msg = rs.message;
                    if (!msg || !rs[v]) return true;
                    if (options.inject) {
                        if (v === options.tipHidden) _clearTip(v, msg);
                        else _injectTip(v, msg);
                    }
                    // else {
                    //     alert(msg);
                    //     callback();
                    // }
                    else Message[v](msg, {
                        hideDelay: options.hideDelay,
                        show: onShow,
                        hide: onHide
                    });
                    return false;
                })) callback();

                function onShow() {
                    if(typeof options.callback === 'function') options.callback(rs, sponsor);
                }
                function onHide() {
                    if (redirect) {
                        if (redirect == 'back') history.back();
                        else if (redirect == 'reload') location.reload();
                        else location.href = redirect;
                    }
                }
                function callback() {
                    onShow();
                    onHide();
                }
            },
            _clearTip = function() {
                if (!inject || !tipElem) return;
                tipElem.remove();
            },
            _injectTip = function(cls, html) {
                var inject = $(options.inject),
                    position = options.position,
                    ajaxTip = options.ajaxTip,
                    tipCls = options.tipCls,
                    cls = cls + tipCls,
                    tipBox;

                if (!inject) return;
                tipBox = inject.getParent();
                tipElem = tipBox.getElement('.' + ajaxTip);
                if (tipBox && tipElem) return tipElem.html(html);
                $('<div class="' + cls + ' ' + ajaxTip +'">' + html + '</div>').appendTo(inject, position);
            },
            _request = function(sponsor) {
                sponsor.addClass(options.disabled);
            },
            _processScripts = function(text, scripts){
                if (scripts) return $.globalEval(text);
                return evalScripts(text, null, true);
            },
            _isCheck = function(options) {
                options = options || {};
                var dataElem = dataForm || options.data;

                if (isElement(dataElem) && !validation(dataElem)) return false;
                return true;
            };
        if (sponsor || sponsor.length) options = _getOptions(sponsor, options);

        options = $.extend({
            disabled: DISABLED,
            /*syncCache: false,
            inject: null,
            tipHidden: false,*/
            hideDelay: 3,
            showMessage: true,
            position: 'before',
            tipCls: '-tip',
            ajaxTip: 'ajax-tip',
            beforeSend: function(xhr, setting) {
                if (sponsor.length) {
                    if (sponsor.hasClass(options.disabled) || !_isCheck() || _progressCache(sponsor)) return false;
                    _request(sponsor);
                }
            },
            success: function(text, status, xhr) {
                response.data = text;
                var header = xhr.getResponseHeader('Content-type');
                if (/application\/json/.test(header)) return _jsonSuccess(text);

                if(options.update) {
                    if(typeof options.callback === 'function') options.callback(xhr);
                    $(options.update).html(text);
                }
                else {
                    _onSuccess(_processScripts(text, (/(ecma|java)script/).test(header)));
                }
                // typeof options.onSuccess === 'function' && options.onSuccess(text, xhr);
            },
            error: function() {
                _defaultState(sponsor);
            }
        }, options || {});

        return options;
    };

    this.async = function(elem, form, options) {
        var sync = elem.data('ajaxCache');
        if (elem.hasClass(DISABLED)) return false;
        if($.isPlainObject(form)) {
            options = form;
            form = null;
        }
        else if (isElement(form)) {
            if (!validation(form, options)) {
                elem.removeClass(DISABLED);
                return false;
            }
            if(options && options.async === false) return;// elem.addClass(DISABLED);
        }
        else if(sync) {
            return $.ajax(sync);
        }
        options = Sync(elem, options);
        if(options.addition) {
            var addition = options.addition.split('::');
            if(addition[0] === 'confirm' && addition[1]) {
                gmu.Dialog({
                    closeBtn: false,
                    mask: true,
                    // title: '提醒',
                    content: '<div style="margin-top:30px;"><p align="center">' + addition[1] + '</p></div>',
                    buttons: {
                        '取消': function(){
                            this.close();
                        },
                        '确定': function(){
                            this.close();
                            return $.ajax(options);
                        }
                    }
                });
            }
            return false;
        }
        return $.ajax(options);
    };

    this.Event_Group = {
        _request: {
            fn: async
        }
    };

})();

$(document).on('click', function(e) {
    var ATTR = 'rel',
        target = $(e.target),
        elem = target.closest('[type=submit]');
    elem = elem.length ? elem : target.closest('[' + ATTR + ']');
    if (!elem[0] || elem[0].nodeType === 9 || elem[0].disabled) return;
    var form = elem.parents('form'),
        type = elem.attr(ATTR),
        eventType = Event_Group[type],
        fn;
    if (form[0] && form.attr('data-async') === 'false') return validation(form);
    if (elem[0].type === 'submit' && form[0] && form.attr('target')) return async(elem, form, {async: false});
    if (elem[0].type === 'submit' && type !== '_request') {
        e.preventDefault();
        return async(elem, form);
    }

    if (eventType) {
        fn = eventType['fn'];

        e.preventDefault();
        if (elem.attr(type)) return elem;

        fn && fn(elem);
    }
});
