/**
 * select search
 *
 * Copyright (c) 2011 Raul Raat
 */
(function ($) {
    var last_match,
        last_regexp,
        default_settings;

    function match(searchterm, item) {
        if (last_match !== searchterm) {
            last_regexp = new RegExp(searchterm, 'i');
            last_match = searchterm;
        }
        return !!item.match(last_regexp);
    }

    var Catselect = function(container, options) {
        this.container = $(container);
        if(!this.container.size()) return;
        this.options = options || {};
        this.url = options.url;
        // this.update = this.container.find(options.update);
        this.select = this.container.find(this.options.select);
        this.selectedList = this.container.find(this.options.selectedList);
        this.selectedParam = this.container.find(this.options.selectedParam);
        this.last_search = '';
        if(!this.url) return;
        return this.init();
    };

    Catselect.prototype.init = function() {
        this.__i = 0;
        this.loadList(this.options.name, this.options.value);
        this.onload && this.onload(this);
        return this.bindEvent(this.container);
    };

    Catselect.prototype.loadList = function(name, value) {
        var instance = this;
        if(this.options.data[value]) {
            this.options.value = value;
            return this.setItems(this.options.data[value]);
        }
        $.post(this.url, name + '=' + value, function(rs) {
            // rs = JSON.parse(rs);
            if(rs.error) {
                $('#messagebox').message(rs.message);
            }

            instance.options.data[value] = rs.data;
            instance.options.value = value;
            instance.setItems(rs.data);

            instance.oncallback && instance.oncallback(instance, rs);
        });
        return this;
    };

    Catselect.prototype.setItems = function(data) {
        this.list = [];
        var select = this.select.eq(this.__i);
        this.select.filter(':gt(' + (this.__i - 1) + ')').html('');
        for (var i in data.options) {
            if (data.options.hasOwnProperty(i)) {
                this.list[i] = this.createItem(data.options[i]);
                select.append(this.list[i]);
                if(data.selectedIndex == i) {
                    this.list[i].trigger('click');
                }
            }
        }
        return this;
    };

    Catselect.prototype.createItem = function(option) {
        var instance = this;
        var data = this.options.data[this.options.value];
        var classlist = this.options.itemClass || '';
        if(option.hasChild) {
            classlist += ' ' + this.options.itemClass + '-haschild';
        }
        var item = $('<a href="' + this.url + '" rel="' + option.value + '" class="' + classlist + '">' + option.text + '</a>');

        item.on('click', function(e) {
            e && e.preventDefault();
            $(this).addClass(instance.options.selectedClass).siblings('.' + instance.options.selectedClass).removeClass(instance.options.selectedClass);
            // for(var i in options) {
            //     if (options.hasOwnProperty(i)) {
            //         if(options[i].selected) {
            //             delete options[i].selected;
            //         }
            //     }
            // }
            data.selectedIndex = '';
            if(option.hasChild) {
                instance.__i = $(this).parent().parent().index() + 1;
                instance.loadList(instance.options.name, option.value);
            }
            else {
                instance.select.filter(':gt(' + $(this).parent().parent().index() + ')').html('');
            }
            var value = [];
            instance.select.find('.' + instance.options.selectedClass).each(function() {
                value.push($(this).text());
            });
            instance.selectedList.html(value.join(' > '));

            if(instance.selectedParam.attr('name') == '') {
                instance.selectedParam.attr('name', instance.options.name);
            }
            if(e.isTrigger) {
                if(!instance.selectedParam.val()) {
                    instance.selectedParam.val(option.value);
                }
            }
            else {
                instance.selectedParam.val(option.value);
            }

            // if(e.isTrigger && instance.selectedParam.attr('name') == '')
            // instance.selectedParam.attr('name', instance.options.name).val(option.value);

            instance.onchange = function (method) {
                if (typeof instance.options.onchange !== 'function') {
                    instance.options.onchange = method;
                    return;
                }
                var old = instance.options.onchange;
                instance.options.onchange = function () {
                    old();
                    method();
                };
            };

            if(instance.options.onchange) {
                instance.options.onchange(instance, option);
            }

        });

        return item;
    };

    Catselect.prototype.bindEvent = function(element) {
        // searchbox
        var instance = this;
        if (this.options.searchbox) {
            this.searchbox = element.find(this.options.searchbox).on('input', function() {
                instance.search($(this).val(), instance.select.eq($(this).parent().parent().index()));
            });
        }
        return this;
    };

    Catselect.prototype.search = function(term, select) {
        var instance = this;

        clearTimeout(this.timeout);
        this.timeout = setTimeout(function() {
            _search(term, select);
        }, this.options.delay);


        function _search(term, select, forced) {
            term = term || '';
            forced = forced === true;
            var i, m;
            if (term === instance.last_search && !forced) {
                return;
            }
            instance.last_search = term;

            var list = select.children();
            for (i in list) {
                if (list.hasOwnProperty(i)) {
                    m = instance.options.match.call(instance, term, list.eq(i).text());
                    if (m === -1) {
                        continue;
                    }
                    if (!term || m) {
                        if (list.eq(i).css('display') === 'none') {
                            list.eq(i).show();
                        }
                    } else if (list.eq(i).css('display') !== 'none') {
                        list.eq(i).hide();
                    }
                }
            }
            instance.select.scrollTop = 0;
        }

        return this;
    };

    var default_settings = {
        url: '',
        name: '',
        value: '0',
        data: {},
        match: match,
        update: null,
        searchbox: null,
        onchange: null,
        delay: 200,
        subs: '.sub-cat',
        select: '.list-group-select',
        itemClass: 'list-group-item',
        selectedList: '.cat-select-foot span',
        selectedParam: '.cat-select-foot input[type=hidden]',
        selectedClass: 'active'
    };

    $.fn.catselect = function(options) {
        options = $.extend(true, default_settings, options || {});

        this.each(function() {
            $(this).data('_catselect_', new Catselect(this, options));
        });
    }
})(jQuery);
