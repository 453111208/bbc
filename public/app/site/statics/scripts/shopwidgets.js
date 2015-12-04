/*shopAdmin Widgets
    Extends DragDropPlus.js
*/
var Widgets = new Class({
    options: {
        /*ddScope: window
        initDrags:function(){},
        initDrops:function(){},
        edit:function(){},
        delete:function(){},
        add:function(){}*/
    },
    init: function(drags, drops, options) {
        this.dragSelecterString = drags;
        this.dropSelecterString = drops;
        this.drags = $(drags);
        this.drops = $(drops);
        this.setOptions(options);
        /*if (this.options.ddScope) {
            this.winScroll = new Scroller(this.options.ddScope, {
                velocity: 1
            });
        }*/
        this.drag_operate_box = $('#drag_operate_box');
        if (!this.drag_operate_box) return;
        this.drag_operate_box.data('lock', false);
        this.drag_handle_box = this.drag_operate_box.find('.drag_handle_box');
        // this.drag_rules = this.drag_operate_box.find('.drag_rules');
        // this.dobFx = this.drag_operate_box.animate(a, 150);
        // this.dhbFx = this.drag_handle_box.animate(b, 150);
        // this.rsFx = new Fx.Morph(this.drag_rules, {
        //     fps: 50,
        //     duration: 150,
        //     link: 'cancel'
        // });

        this.dragSign = $('#drag_ghost_box').appendTo(document.body);
        this.trigger('init',this);

        this.initDOBBase(this.drops);
        this.initDrags(this.drags);
        this.initDrops(this.drops);

        this.drags.each(function() {
            if ($(this).attr('ishtml')) {
                $(this).find('.content-html').html($(this).find('.content-textarea').val());
            }
        });
    },
    checkEmptyDropPanel: function(dp) {
        if (!dp || !$(dp).hasClass(this.dropSelecterString.substring(1, this.dropSelecterString.length))) return;
        if (!$(dp).find(this.dragSelecterString).size()) {
            if (!$(dp).find('.empty_drop_box').size()) {
                var emptyBox = $('<div class="empty_drop_box"></div>').html('&nbsp;<button type="button" class="btn btn-add-widgets"><span><span><i class="icon"></i>添加挂件</span></span></button>').appendTo(dp);
                emptyBox.on('click', function(e) {
                    this.trigger('add', [emptyBox], this);
                }.bind(this));
                if (this.dragmoveInstance) {
                    $(dp).data('droppanel', true);
                    this.dragmoveInstance.droppables.push(dp);
                }
            }
        } else {
            $(dp).find('.empty_drop_box').remove();
        }
    },
    initDOBBase: function(drops) {
        var dob = this.drag_operate_box;
        var dhb = this.drag_handle_box;
        var _this = this;
        if (!drops) return;

        var updown = dhb.find('.btn-up-slot,.btn-down-slot');
        updown.on('click',function(e){
            e.preventDefault();
            e.stopPropagation();
            var drag = dob.data('drag');
            if(!drag || !drag.size()) return;
            var els = drag.parent().children();
            var swap;
            var where;
            if($(this).hasClass('btn-up-slot')) {
                swap = drag.prev();
                where = 'after';
            }
            else {
                swap = drag.next();
                where = 'before';
            }
            if(swap.size()) {
                drag[where](swap);
                $(document.body).trigger('mouseover',{target: drag});
                _this.trigger('upDown',[dob.data('drag')], _this);
            }
        });
        dhb.find('.btn-edit-widgets').on('click', function(e) {
            e.preventDefault();
            _this.trigger('edit', [dob.data('drag')], _this);
        });
        dob.on('dblclick', function(e) {
            e.preventDefault();
            _this.trigger('edit', [dob.data('drag')], _this);
        });
        dhb.on('dblclick', function(e) {
            e.preventDefault();
        });
        dhb.find('.btn-del-widgets').on('click', function(e) {
            e.preventDefault();
            _this.trigger('delete', [dob.data('drag')], _this);
        });
        dhb.find('li').on('click', function(e) {
            e.stopPropagation();
            _this.trigger('add', [dob.data('drag'), $(e.target)], _this);
        });
    },
    initDrags: function(drags) {
        var _this = this;
        var dob = this.drag_operate_box;
        var dhb = this.drag_handle_box;
        // var rule = this.drag_rules;
        var toStyles;
        $(document.body).on('mouseover', function(e) {
            e = $(e.target);
            var drag = e.parents(_this.dragSelecterString);
            var minWidth = 235;
            if(!drag.size() && !e.hasClass(_this.dragSelecterString.substr(1))) return;
            if(dob.data('lock')) return;
            drag = drag.size() ? drag : e;
            _this.trigger('initDrags', [drag, drags], _this);
            dhb.attr('title', drag.attr('title') || "&nbsp;");
            dob.css('visibility', 'visible');
            dob.data('drag', drag);
            toStyles = {
                top: drag.offset().top - dhb.height(),
                left: drag.offset().left,
                height: drag.height() - dob.patch().y + dhb.height(),
                width: drag.width() - dob.patch().x
            };
            var dobW =  minWidth + dob.patch().x;
            _this.drag_handle_box.css({left:toStyles.left + dobW + parseInt(dob.css('border-left')) > $(document.body).width() ? toStyles.width - dobW : 0});
            _this.drag_operate_box.css(toStyles);

            dhb.find('.btn-up-slot,.btn-down-slot').removeClass('disabled');
            if(!drag.prev().size()) dhb.find('.btn-up-slot').addClass('disabled');
            if(!drag.next().size()) dhb.find('.btn-down-slot').addClass('disabled');
        });
        // dhb.addEvents({
        //     'mouseenter': function(e){
        //         if(toStyles && toStyles.width >= 50) {
        //             rule.show().getElement('.drag_annotation').setStyle('width', toStyles.width - rule.getElement('.drag_left_arrow').getSize().x * 2).getElement('em').set('text', toStyles.width + dob.getPatch().x + 'px');
        //             var height = 20;
        //             _this.rsFx.cancel();
        //             _this.rsFx.start({'height':height,'line-height':height});
        //         }
        //     },
        //     'mouseleave': function(e){
        //         if(toStyles && toStyles.width >= 50) {
        //             _this.rsFx.cancel();
        //             _this.rsFx.start({'height':0,'line-height':0});
        //         }
        //     }
        // });
        $(function(){
            drags.each(function(){
                _this.checkEmptyDrag(this);
                $(this).find('form').off().on('submit', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
                $(this).find('a').off().on('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });
        });
    },
    checkEmptyDrag: function(drag){
        if(!$(drag).outerHeight()){
            this.trigger('emptyDrag',[drag],this);
        }
    },
    initDrops: function(drops) {
        var _this = this;
        $.each(drops, function(index) {
            _this.checkEmptyDropPanel(this);
            _this.trigger('initDrops', [this, drops], _this);
        });
    },
    inject: function(widget, theme) {
        this.addWidget(this.curEl, widget, theme || this.theme);
    },
    ghostDrop: function(widget, theme) {
        widget = $.type(widget) === 'string' ? $.parseJSON(widget) : widget;
        this.drag_operate_box.css('visibility', 'hidden').data('lock', true);
        $('#tempDropBox').remove();
        this.tempDropBox = $('<div id="tempDropBox"></div>').appendTo(document.body);
        try {
            var item = this.drag_operate_box.data('drag');
            this.tempDropBox.empty();
            this.addWidget(item, widget, theme);
            this.drag_operate_box.data('lock', false);
        } catch(e) {
            alert(JSON.stringify(e));
        }
        var _this = this;
        $(document.body).on('contextmenu', function(e) {
            e.preventDefault();
            $('#tempDropBox').remove();
            _this.drag_operate_box.data('lock', false);
            $(this).off('contextmenu', arguments.callee);
        });
    },
    addWidget: function(drop, widget, theme) {
        var dialogSetting = {
            modal: true,
            title: '添加挂件 ' + (widget.label || ''),
            ajaxoptions: {
                render: false
            },
            width: 0.7,
            height: 0.7
        };
        this.curdialog = new top.Dialog(top.SHOPADMINDIR + '?app=site&ctl=admin_theme_widget&act=do_add_widgets&widgets=' + widget.name + '&widgets_app=' + widget.app + '&widgets_theme=' + widget.theme + '&theme=' + theme, dialogSetting);
        this.curDrop = drop;
    },
    editWidget: function(widget) {
        var dialogSetting = {
            modal: true,
            title: '编辑挂件 ' + (widget.label || widget.attr('title') || ''),
            ajaksable: false,
            width: 0.7,
            height: 0.7
        };
        this.curWidget = $(widget);
        if (widget.attr('ishtml')) return this.curdialog = new top.Dialog(top.SHOPADMINDIR + '?ctl=content/pages&act=editHtml', $.extend(dialogSetting, {
            ajaksable: true,
            ajaxoptions: {
                method: 'post',
                data: 'htmls=' + encodeURIComponent(widget.find('.content-html').html().clean().trim())
            },
            title: '编辑HTML'
        }));

        return this.curdialog = new top.Dialog(top.SHOPADMINDIR + '?app=site&ctl=admin_theme_widget&act=do_edit_widgets&widgets_id=' + widget.attr('widgets_id') + '&theme=' + widget.attr('widgets_theme'), dialogSetting);

    },
    delWidget: function(widget) {
        var dob = this.drag_operate_box;
        var _this = this;
        dob.css('visibility', 'hidden').data('lock', true);
        var drop = widget.parent();
        widget.animate({'opacity': 0}, {
            duration: 250,
            complete: function() {
                widget.remove();
                dob.data('lock', false);
                _this.checkEmptyDropPanel(drop[0]);
                try {
                    top.document.getElementById('btn_save').disabled = false;
                } catch(e) {}
            }
        });
    },
    preview: function(url,target){
        var params = [];
        var wpanels = this.drops;
        var file = {};
        var _this = this;
        wpanels.each(function(index, item) {
            var widgets = $(item).find('.shopWidgets_box');
            var selfdata = {
                // mce: _this.mce,
                bf: $(item).attr('base_file'),
                bs: $(item).attr('base_slot'),
                bi: $(item).attr('base_id')
            };
            widgets.each(function(index, widgetbox) {
                params.push(substitute("widgets[{widgetsId}]={baseFile}:{baseSlot}:{baseId}", {
                    widgetsId: $(widgetbox).attr('widgets_id'),
                    baseFile: selfdata.bf,
                    baseSlot: selfdata.bs,
                    baseId: selfdata.bi
                }));

                if ($(widgetbox).attr('ishtml')) {
                    var ch = $(widgetbox).find('.content-html');
                    params.push(substitute('html[{widgetsId}]={htmls}', {
                        widgetsId: $(widgetbox).attr('widgets_id'),
                        htmls: encodeURIComponent(ch.html())
                    }));
                }
            });
            file[$(item).attr('base_file')] = 1;
        });

        for (f in file) {
            params.push('files[]=' + f);
        }

        $.ajax(url, {
            type: 'post',
            data: params.join('&'),
            beforeSend:function(){
                $(target).prop('disabled',true).html('<span><span>正在生成预览...</span></span>');
            },
            success:function(rs){
                rs = $.parseJSON(rs);
                $(target).prop('disabled', false).html('<span><span>预览模板</span></span>');
                if(rs && rs.success){
                  //模拟a事件点击以在新窗口打开预览页面->by TylerChao
                  var a = document.getElementById('_temp_preview_link') || $('<a href="' + (rs.url || top.PREVIEW_URL) + '" id="_temp_preview_link" class="hide" target="preview"></a>').appendTo(document.body)[0];
                  if(document.createEvent) {
                      var evt = document.createEvent('MouseEvent');
                      evt.initEvent('click', false, false);
                      a.dispatchEvent(evt);
                  }
                  else a.click();
                }
            }
        });
    },
    saveAll: function(fn,bind) {
        var params = [];
        var wpanels = this.drops;
        var file = {};
        var _this = this;
        wpanels.each(function(index, item) {
            var widgets = $(item).find('.shopWidgets_box');
            var selfdata = {
                // mce: _this.mce,
                bf: $(item).attr('base_file'),
                bs: $(item).attr('base_slot'),
                bi: $(item).attr('base_id')
            };
            widgets.each(function(index, widgetbox) {
                params.push(substitute("widgets[{widgetsId}]={baseFile}:{baseSlot}:{baseId}", {
                    widgetsId: $(widgetbox).attr('widgets_id'),
                    baseFile: selfdata.bf,
                    baseSlot: selfdata.bs,
                    baseId: selfdata.bi
                }));

                if ($(widgetbox).attr('ishtml')) {
                    var ch = $(widgetbox).find('.content-html');
                    params.push(substitute('html[{widgetsId}]={htmls}', {
                        widgetsId: $(widgetbox).attr('widgets_id'),
                        htmls: encodeURIComponent(ch.html())
                    }));
                }
            });
            file[$(item).attr('base_file')] = 1;
        });

        for (f in file) {
            params.push('files[]=' + f);
        }

        $.ajax(top.SHOPADMINDIR + '?app=site&ctl=admin_theme_widget&act=save_all', {
            type:'post',
            data:params.join('&'),
            beforeSend: function() {
                top.MessageBox.show('正在保存');
            },
            success: function(re) {
                fn && fn.call(bind||_this,_this);
                try {
                    re = $.parseJSON(re);
                    for (dom in re) {
                        if ($(dom) && re[dom]) $(dom).attr('widgets_id', re[dom]);
                    }
                    top.MessageBox.success('保存成功。');
                } catch(e) {
                    top.MessageBox.error('​保存失败：' + e.message);
                }
            }
        });
    }
});

$(function() {
    window.shopWidgets = new Widgets('.shopWidgets_box', '.shopWidgets_panel', {
        init:function(){
            this.theme = top.THEME_NAME||'';
        },
        edit: function(widget, widget_panel) {
            this.editWidget(widget);
        },
        delete: function(widget) {
            var _this = this;
            if(top.confirmDialog) {
                top.confirmDialog('确定删除此挂件吗？',function(){
                    _this.delWidget(widget);
                });
            }
            else {
                if (confirm('确定删除此挂件吗？')) this.delWidget(widget);
            }
        },
        add:function(widget,el) {
            var dob = this.drag_operate_box;
            var where = el ? el.attr('class') || '' : '';
            var _this = this;
            dob.data('lock', true);
            this.widgetsDialog = new top.Dialog(top.SHOPADMINDIR + '?app=site&ctl=admin_theme_widget&act=add_widgets_page&theme=' + this.theme,{
                    width:770,
                    height:500,
                    title:'添加挂件',
                    modal:true,
                    resizeable:false,
                    onShow:function(e){
                        this.dialog_body.id='dialogContent';
                        if(widget.hasClass('empty_drop_box')) _this.injectBox = widget.parent();
                        else {
                            _this.injectWhere = where;
                            _this.injectBox = null;
                        }
                    },onClose:function(){
                        dob.data('lock', false);
                    }
            });
        },
        upDown:function(widget,el){
            top.document.id('btn_save') && (top.document.id('btn_save').disabled = false);
        },
        emptyDrag:function(widget){
            $('<div class="empty_drag_box">(TEMP DATA)</div>').appendTo(widget);
        }
    });
});

