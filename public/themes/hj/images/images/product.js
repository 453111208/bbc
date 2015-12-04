$('.btnw .btn').bind('click',function(){
	$(this).next().toggle()

});
//Ò³¿¨ÇÐ»»º¯Êý
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
Tabs($('.max-box-xuqiu'))
Tabs($('.max-box-hot'))


//µ­³öµ­Èë

$('.biddin-item ').each(function(){
	$(this).find('.name').hover(function(){
		$(this).parents('table').next('.max-bd-popup').fadeIn(0);
	},
	function(){
		$(this).parents('table').next('.max-bd-popup').fadeOut(0)
	
	})
})
