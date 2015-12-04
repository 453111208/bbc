/*地区选择 by zhangxin*/
var AreaWidget = function(options){
    var self = this;
    self.options = {
        dataUrl: 'data.json',
        select: null,
        level: 3,
        name: 'area[]',
        initData: null
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
                item.val(initData[index]).trigger('change');
            });
        }else{
            self.input = $('<input type="hidden" name="' + self.options.name + '">').appendTo(self.box);
        }
    }

    var freshTheList = function(){
        var el = $(this),
            index = el.find('option:selected').index() - 1,
            level = el.index();
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
