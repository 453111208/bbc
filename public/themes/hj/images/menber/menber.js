/*左侧菜单点击*/
		$(".max-menber-menu").delegate("h2","click",function(){
		    var _thisMeneDetail = $(this).next(".max-menu-ul")
		        ,_menuList = $(this).parents(".max-menu-mod");
		    //_menuList.addClass("artive").siblings(".max-menu-mod").removeClass("artive");
		    if(_thisMeneDetail.is( ":visible")){
		        _menuList.removeClass("artive");
		        _thisMeneDetail.slideUp();
		        return false;
		    }else{
		       // $(".max-menu-ul",".max-menu-mod").slideUp();
			    _menuList.addClass("artive");
		        _thisMeneDetail.slideDown();
		        return false;
		    }
		}); 

		//页卡切换
		function Tabs(top,body,shijian){
	top.find('.max-the-bidder').each(function(i){
		$(this).bind(shijian,function(){
					$(this).addClass('Hover').siblings('.max-the-bidder').removeClass('Hover');
			if (body)
			{
			
			body.find('.max-the-Contents').eq(i).css('display','block').siblings('.max-the-Contents').hide()

				};
	
		})
	
		}
	)
 
 
 };

//左侧菜单点击 END

//文章切换
Tabs($('.max-highest-bidder'),$('.max-highest-bidder'),'click')//mousemove,click
//文章切换 END



//设置商品数量
bindQuantityEvent('.lakers_nums', setQuantity);
//== 为数量选择框绑定事件
function bindQuantityEvent(elements, callback) {
  elements = $(elements);
  if(!elements && !elements.length) return;
  var value = '';
  //= 数量按钮
  elements.on('click', '.btn-quantity-decrease,.btn-quantity-increase', function (e) {
    var input = $(this).parent().find('.action-quantity-input');
    value = + input.val();
    input.val($(this).hasClass('btn-quantity-decrease') ? value - 1 : value + 1);
    callback && callback(input, value);
  })
  //= 数量输入框
  .on('focus', '.action-quantity-input', function(e){
    value = +this.value;
  })
  .on('change', '.action-quantity-input', function(e) {
    callback && callback($(this), value);
  });
}
//== 获取商品数量值
function getQuantity(el, type) {
  return el.find('input[name=' + type + ']').val();
}


//== 商品数量输入框正确性检测
function inputCheck(input, options) {
  if(!input && !input.length) return false;


  options = options || {};
  if(isNaN(options.min)) options.min = 1;
  if(isNaN(options.max)) options.max = 999999;
  options['default'] = options['default'] || options.min;
  var value = +input.val();
  // var tips = new Tips(input);
  var pre = '';
  var msg = '';
  if(options.store && options.store - value <= 0) {
    pre = '库存有限，';
  }
  if(value < options.min) {
    input.val(options.min);
    msg = '此商品的最小购买数量为' + options.min + '件';
  }
  else if(value > options.max){
    input.val(options.max);
    msg = pre + '此商品最多只能购买' + options.max + '件';
  }
  else if(isNaN(value)) {
    input.val(options['default']);
    msg = '只允许输入数字';
  }
  if (msg) {
    // tips.show(msg);
    Message.error(msg);
    return false;
  }
  // tips.hide();
  if(options.callback) options.callback(input, options['default']);
  return true;
}

//== 设置商品数量
function setQuantity(input, value) {
  var type = 'product';
  var p = input.parent('li');
  inputCheck(input, {min: input.attr('min'), max: input.attr('max'), 'default': value, store: getQuantity(p, 'stock'), callback: window.quantityCallback});
}
//设置商品数量 END

//设置商品单价
bindQuantityEventPrice('.lakers_price', setPrice);
//== 为数量选择框绑定事件
function bindQuantityEventPrice(elements, callback) {
  elements = $(elements);
  if(!elements && !elements.length) return;
  var value = '';
  //= 数量按钮
  elements.on('click', '.btn-price-decrease,.btn-price-increase', function (e) {
    var input = $(this).parent().find('.action-price-input');
    value = + input.val();
    input.val($(this).hasClass('btn-price-decrease') ? value - 1 : value + 1);
    callback && callback(input, value);
  })
  //= 数量输入框
  .on('focus', '.action-price-input', function(e){
    value = +this.value;
  })
  .on('change', '.action-price-input', function(e) {
    callback && callback($(this), value);
  });
}
//== 获取商品数量值
function getPrice(el, type) {
  return el.find('input[name=' + type + ']').val();
}


//== 商品数量输入框正确性检测
function inputCheckprice(input, options) {
  if(!input && !input.length) return false;
  options = options || {};
  if(isNaN(options.min)) options.min = 1;
  if(isNaN(options.max)) options.max = 99999999999;
  options['default'] = options['default'] || options.min;
  var value = +input.val();
  // var tips = new Tips(input);
  var pre = '';
  var msg = '';

   if(value < options.min) {
    input.val(options.min);
    msg = '最小金额为' + options.min;
  }

  if (msg) {
    // tips.show(msg);
    Message.error(msg);
    return false;
  }
  // tips.hide();
  if(options.callback) options.callback(input, options['default']);
  return true;
}

//== 设置商品数量
function setPrice(input, value) {
  var type = 'product';
  var p = input.parent('li');
  inputCheckprice(input, {min: input.attr('min'), max: input.attr('max'), 'default': value, store: getPrice(p, 'stock'), callback: window.quantityCallback});
}
//设置商品单价 END


function setValue(str,el){
		$('.standard_stop_time')[0].value=str;
		$(el).addClass('active');
}
//设置日期
$('.lakers_month').click(function(){
	$('.lakers_month').removeClass('active');
	if($(this).hasClass('one')){
		setValue('one',this);
	}else if($(this).hasClass('three')){
		setValue('three',this);
	}
	else if($(this).hasClass('six')){
		setValue('six',this);
	}
	else if($(this).hasClass('december')){
		setValue('december',this);
	}
	else if($(this).hasClass('effective')){
		setValue('effective',this);
	}			
	
});
