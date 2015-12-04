
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
 //文章切换
Tabs($('.max-highest-bidder'),$('.max-highest-bidder'),'click')//mousemove,click

//页卡切换
		function Tabse(top,body,shijian){
	top.find('.max-card-ul li').each(function(i){
		$(this).bind(shijian,function(){
					$(this).addClass('Hover').siblings('.max-card-ul li').removeClass('Hover');
			if (body)
			{
			
			body.find('.max-p-card').eq(i).css('display','block').siblings('.max-p-card').hide()

				};
		
	
		})
	
		}
	)
 
 
 };
 //文章切换
Tabse($('.max-Page-card'),$('.max-Page-card'),'mousemove')//mousemove,click


		//页卡切换
		function Tabss(top,body,shijian){
	top.find('.max-head-but span').each(function(t){
		$(this).bind(shijian,function(){
					$(this).addClass('Hover').siblings('.max-head-but span').removeClass('Hover');
			if (body)
			{
			
			body.find('.max-In-news').eq(t).css('display','block').siblings('.max-In-news').hide()

				};
		
	
		})
	
		}
	)
 
 
 };
 //文章切换
Tabss($('.max-Infor-right'),$('.max-Infor-right'),'click')//mousemove,click
