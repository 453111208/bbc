(function($) {
//读取json树并按照其结构生成带多选的dom树。
$.fn.jsonList = function(options) {
    return this.each(function() {
        $(this).data('JSONList', new JSONList(this, options));
    });
};
var JSONList = new Class({
    idPrefix: 'checkbox_',
    _uniqueId: 0,
    options: {
        url: null,
        data: null,
        items: 'children',
        label: 'name',
        name: '',
        response: function(data, textStatus) {},
        success: function(jsonList) {},
        listItem: function(listItem, data, isGroup) {},
        showSubList: function(subList) {},
        hideSubList: function(subList) {}
    },
    init: function(container, options) {
        this.container = $(container);
        // fill options with default values
        this.setOptions(options);
        options = this.options;
        var self = this;
        $.getJSON(options.url, options.data, function(data, textStatus) {
            self.data = data;
            self.trigger('response', [self, textStatus]);
            self.handleSuccess(data, textStatus);
            self.trigger('success', [self, textStatus]);
        });
    },
    handleSuccess: function (data, status) {
        if(status == 'success') {
            // this.build(data);
            this.attachEvent();
            this.container.addClass('tree-list');
        }
        else {
            alert('网络错误，请重试。');
        }
        return this;
    },
    build: function (data) {
        if(!data) return;
        var list = ['<ol class="primary clearfix">'];
        var self = this;
        $.each(data, function() {
            list.push(self.createItem(this));
        });
        list.push(this.createMarginItem());
        list.push('</ol>');
        this.container.html(list.join('\n'));
        var checkbox = this.container.find('.has-children > label > input[type=checkbox]');
        $.each(data, function() {
            if(this.disabled) return;
            var json = this;
            checkbox.each(function() {
                if(json.indeterminate && this.value == json.id) { 
                    this.indeterminate = true;
                }
            });
        });
        this.marginItem = this.container.find('.margin-element');
        return this;
    },
    rebuild: function(disabledIds, checkedIds) {
        this.container.empty();
        this.changeStatus(this.data, disabledIds, checkedIds);
        this.build(this.data);
        return this;
    },
    createItem: function (json) {
        if(json.disabled) return '';
        var cls = 'col-md-2 item collapsed';
        var thumb = '';
        var children = '';
        var items = this.options.items;
        if(json[items]) {
            cls += ' has-children';
            thumb = this.createThumb();
            children = this.createChildren(json[items]);
        }
        var item = [
            '<li class="' + cls + '">',
            '<label>',
            this.createCheckbox(json),
            json[this.options.label],
            '</label>',
            thumb,
            children,
            '</li>'
        ];
        return item.join('\n');
    },
    createCheckbox: function (json) {
        var checkbox = '<input type="checkbox" name="' + this.options.name + '[]" value="' + json.id + '" ' + (json.checked ? 'checked' : '') + ' ' + (json.disabled ? 'disabled' : '') + '>';
        return checkbox;
    },
    createMarginItem: function(list) {
        return '<li class="margin-element"></li>';
    },
    createThumb: function () {
        return '<i class="thumb"></i>';
    },
    createChildren: function (json) {
        var self = this;
        var list = ['<ol class="sub clearfix">'];
        if(json && json.length) {
            $.each(json, function(i) {
                list.push(self.createSubItem(this));
            });
        }
        list.push('</ol>');
        return list.join('\n');
    },
    createSubItem: function (json) {
        return json.disabled ? '' : [
            '<li class="col-md-3">',
            '<label>',
            this.createCheckbox(json),
            json[this.options.label],
            '</label>',
            '</li>'
        ].join('\n');
    },
    itemSelector: 'li',
    attachEvent: function () {
        var self = this;
        this.container.on('click', '.thumb', function () {
            self.toggle($(this).parent(self.itemSelector));
        })
        .on('change', '.has-children > label > input[type=checkbox]', function(e) {
            var isChecked = this.checked;
            var parent = $(this).parents(self.itemSelector).eq(0);
            var subCheckboxes = parent.find('.sub input[type=checkbox]');
            // if(subCheckboxes.filter(':disabled').length) {
            //     self.setIndeterminate(this, isChecked);
            // }
            subCheckboxes.prop('checked', isChecked);
            self.setChecked(this, isChecked);
        })
        .on('change', '.sub input[type=checkbox]', function(e) {
            var isChecked = this.checked;
            var indeterminate = false;
            var parent = $(this).parents('.sub');
            var checkbox = parent.siblings('label').find('input[type=checkbox]')[0];
            var subCheckboxes = parent.find('input[type=checkbox]');
            // if(subCheckboxes.filter(':disabled').length) {
            //     self.setIndeterminate(checkbox, isChecked);
            //     indeterminate = true;
            // }
            if(!subCheckboxes.filter(isChecked ? ':not(:checked)' : ':checked').length) {
                checkbox.indeterminate = false;
                checkbox.checked = isChecked;
            }
            else {
                self.setIndeterminate(checkbox);
                indeterminate = true;
            }
            self.setChecked(this, isChecked, indeterminate);
        });
        return this;
    },
    setDisabledChecked: function(element, isChecked) {
        var items = this.options.items;
        var id = element.value;
        $.each(this.data, function () {
            if(this.id == id) {
                this.checked = isChecked;
                if(this[items] && this[items].length) {
                    $.each(this[items], function(i) {
                        if(!this.disabled) this.checked = isChecked;
                    });
                }
            }
        });
        return this;
    },
    setChecked: function (element, isChecked, isIndeterminate) {
        var items = this.options.items;
        var id = element.value;
        // element.indeterminate = false;
        $.each(this.data, function () {
            if(this.id == id) {
                if(!isIndeterminate) {
                    delete this.indeterminate;
                    this.checked = isChecked;
                }
                if(this[items] && this[items].length) {
                    $.each(this[items], function() {
                        if(!this.disabled) this.checked = isChecked;
                    });
                }
            }
            else if(this[items] && this[items].length) {
                var index = 0;
                var disIndex = 0;
                $.each(this[items], function() {
                    if(this.id == id) {
                        this.checked = isChecked;
                    }
                    if(this.disabled) disIndex++;
                    else if(this.checked === isChecked) index++;
                });
                if(!isIndeterminate && index && index === this[items].length - disIndex) {
                    delete this.indeterminate;
                    this.checked = isChecked;
                }
            }
        });
        return this;
    },
    setIndeterminate: function(element) {
        element.indeterminate = true;
        element.checked = false;
        $.each(this.data, function () {
            if(this.id == element.value) {
                this.indeterminate = true;
                delete this.checked;
            }
        });
        return this;
    },
    changeStatus: function (data, disabledIds, checkedIds) {
        var items = this.options.items;
        var self = this;
        if($.isArray(data)) {
            $.each(data, function() {
                self.changeStatus(this, disabledIds, checkedIds);
                // console.log(this);
            });
        }
        else {
            var json = data;
            if(disabledIds) {
                if(disabledIds.length) {
                    if (disabledIds.indexOf(json.id) >= 0) {
                        json.disabled = true;
                        if(json[items]) {
                            $.each(json[items], function() {
                                this.disabled = true;
                            });
                        }
                    }
                    else if(json[items] && json[items].length) {
                        var index = 0;
                        $.each(json[items], function() {
                            if(disabledIds.indexOf(this.id) >= 0) {
                                this.disabled = true;
                                index++;
                            }
                            else {
                                delete this.disabled;
                            }
                            if(index && index === json[items].length) {
                                json.disabled = true;
                            }
                            else {
                                delete json.disabled;
                            }
                        });
                    }
                    else {
                        delete json.disabled;
                        if(json[items] && json[items].length) {
                            $.each(json[items], function() {
                                delete this.disabled;
                            });
                        }
                    }
                }
                else {
                    delete json.disabled;
                    if(json[items] && json[items].length) {
                        $.each(json[items], function() {
                            delete this.disabled;
                        });
                    }
                }
            }
            if (checkedIds) {
                if(checkedIds.length) {
                    if (checkedIds.indexOf(json.id) >= 0) {
                        json.checked = true;
                        if(json[items] && json[items].length) {
                            $.each(json[items], function() {
                                this.checked = true;
                            });
                        }
                    }
                    else if(json[items] && json[items].length) {
                        var index = 0;
                        var disIndex = 0;
                        $.each(json[items], function() {
                            if(checkedIds.indexOf(this.id) >= 0) {
                                this.checked = true;
                                index++;
                            }
                            else {
                                delete this.checked;
                            }
                            if(this.disabled) disIndex++;
                        });
                        if(index) {
                            var len = json[items].length - disIndex;
                            if(index === len) {
                                delete json.indeterminate;
                                json.checked = true;
                            }
                            else if(index < len) {
                                delete json.checked;
                                json.indeterminate = true;
                            }
                        }
                        else {
                            delete json.checked;
                            delete json.indeterminate;
                        }
                    }
                    else {
                        delete json.checked;
                        delete json.indeterminate;
                        if(json[items] && json[items].length) {
                            $.each(json[items], function() {
                                delete this.checked;
                            });
                        }
                    }
                }
                else {
                    delete json.checked;
                    delete json.indeterminate;
                    if(json[items] && json[items].length) {
                        $.each(json[items], function() {
                            delete this.checked;
                        });
                    }
                }
            }
        }
        return this;
    },
    toggle: function (item) {
        return this[item.hasClass('expanded') ? 'collaspe' : 'expand'](item);
    },
    expand: function (item) {
        var sub = item.find('.sub');
        item.addClass('expanded').removeClass('collapsed')
            .siblings('.expanded')
            .addClass('collapsed').removeClass('expanded')
        sub.css('left', - item.position().left);
        this.getLineItem(item);
        this.marginItem.css('margin-bottom', sub.height() + 20);
        return this.trigger('showSubList', sub);
    },
    collaspe: function (item) {
        item.addClass('collapsed').removeClass('expanded');
        this.marginItem.css('margin-bottom', '');
        return this.trigger('hideSubList', item.find('.sub'));
    },
    getLineItem: function (el) {
        var element = el;
        var next = el.nextAll();
        if(next.length) {
            next.each(function (i) {
                if($(this).position().top !== el.position().top) {
                    element = $(this);
                    return false;
                }
                return true;
            });
            element.before(this.marginItem);
        }
        else {
            element.after(this.marginItem);
        }
        return element;
    },
    getChecked: function (keys) {
        var self = this;
        var items = this.options.items;
        var selected = [];
        if(typeof keys === 'string') keys = [keys];
        $.each(keys, function(i, key) {
            selected[i] = [];
            $.each(self.data, function () {
                if(this.checked) {
                    if(this[items] && this[items].length) {
                        for(var j = 0, l = this[items].length, m; j < l; j ++) {
                            m = this[items][j];
                            if(m.disabled) {
                                getSubChecked(i, this, key);
                                return;
                            }
                        }
                    }
                    selected[i].push(this[key]);
                }
                else if(this.indeterminate) {
                    getSubChecked(i, this, key);
                }
            });
        });
        function getSubChecked(i, json, key) {
            if(key == 'value') {
                var value = [json[key]];
                var children = [];
                $.each(json[items], function() {
                    if(this.checked) {
                        children.push(this[key]);
                    }
                });
                if(children.length) {
                    value.push('<em class="text-muted">(', children.join(), ')</em>');
                }
                selected[i].push(value.join(''));
            }
            else {
                $.each(json[items], function() {
                    if(this.checked) {
                        selected[i].push(this[key]);
                    }
                });
            }
        }
        return selected;
    },
    isDisabled: function() {
        var index = 0;
        $.each(this.data, function () {
            if(this.disabled) {
                index++;
            }
        });
        return index && index === this.data.length;
    }
});

}(jQuery));
