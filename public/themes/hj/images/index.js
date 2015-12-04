//页卡切换函数
 function Tabs(top,body){
	top.find('li').each(function(i){
		$(this).hover(function(){
			$(this).addClass('active').siblings().removeClass('active');
			if (body)
			{
			
			body.find('.maxcomon').eq(i).css('display','block').siblings().hide()

				};
		
		})
	
		}
	)
 
 
 };
 //文章切换
Tabs($('.maxArticle .max-Tabs-top'),$('.maxArticle .max-Tabs-body'))
//交易中心左侧切换
Tabs($('.maxPro-L .max-Tabs-top'),$('.maxPro-L .max-Tabs-body'))
//交易中心左侧切换(类似排行榜)
Tabs($('.maxPro-L .max-Tabs-body'))
Tabs($('.maxPro-C .max-Tabs-top'),$('.maxPro-C .max-Tabs-body'))
Tabs($('.maxPro-C .max-Tabs-body'))
Tabs($('.maxPro-L.max-hot'))
Tabs($('.maxPro-R .max-Tabs-top'),$('.maxPro-R .max-Tabs-body'))
Tabs($('.f3 .maxPro-R'))
