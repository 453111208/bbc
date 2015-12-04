var region_sel = {
    addOpt:function(select, path) {
        var _this = this;
        var html = ['<option value="_NULL_">请选择</option>'];
        if(path) {
            path.each(function(v, i){
                //var attrs = v.split(':');
                //var lv = '';
                //if(attrs[2]) {
                //    lv = ' data-level-index="' + attrs[2] + '"';
                //}
                //html.push('<option value="' + attrs[1] + '"' + lv + '>' + attrs[0] + '</option>');
                html.push('<option value="' + v + '">' + _this.data[v].value + '</option>');
            });
            if(select) {
                select.set('html', html.join('')).show();
            }
            else {
                new Element('select', {
                    events: {
                        'change': function(e) {
                            _this.changeResponse(this);
                        }
                    }
                }).inject(this.elem, 'bottom').set('html', html.join(''));
                this.sels = this.elem.getElements('select');
            }
        }
        return this;
    },
    attachEvent:function(){
        var _this = this;
        this.sels.addEvent('change', function(e){
            _this.changeResponse(this);
        });
    },
    changeResponse:function(cur_sel, opt){
        var path = this.set(cur_sel, opt);
        var elems = cur_sel.getAllNext();

        if(this.callback) {
            _this.callback(_this.sels);
        }
        elems.each(function(el,i){
            if(i || elems.length == 1) el.hide().empty();
        })
        this.addOpt(cur_sel.getNext(), path).setValue();
    },
    setValue:function(){
        var k = [],str,id = [];
        this.sels.each(function(el){
            var opt = el.getSelected(), t = opt.get('text'), v = opt.get('value');
            if(opt.length && v!='_NULL_') {
                k.push(t);
                id.push(v);
            }
        });

        if(k.length) {
            str = k.join(',');
            id = id.join(',');
            this.elem.getElement('input').value=str+':'+id;
        }
        else {
            this.elem.getElement('input').value = '';
        }
    },
    isAddSel:function(select){
        select.getAllNext().each(function(el){el && el.empty().hide();});
        return this;
        // select.getNext() && select.getNext().empty();
    },
    set:function(target, opt){
        if(opt) {
            opt.selected = true;
        }
        else {
            opt = target.options[target.selectedIndex];
        }
        var index = opt.value;
        var path = this.path[index];
        return path;
    },
    init:function(container, data, path, func_callback){
        this.callback = window[func_callback];
        this.elem = container;
        this.sels = this.elem.getElements('select');
        this.data = JSON.decode(data);
        this.path = JSON.decode(path);
        //this.addOpt(this.sels[0], this.data, this.path).isAddSel(this.sels[0].show());
        this.attachEvent();
    }
};
