
$(document).ready(function(){
$('.maxrank .maxTabs-body li').each(function(){
	$(this).hover(function(){
		$(this).addClass('active').siblings().removeClass('active');
		})
})
$('.f2 .maxTabs li').each(function(i){$(this).hover(function(){
		$(this).addClass('current').siblings().removeClass('current');
		$(this).parents('.item').find('.maxTabs-body').eq(i).addClass('cur').siblings().removeClass('cur');
		
		})
			})

$('.maxrank .maxTabsnew-body li').each(function(){
	$(this).hover(function(){
		$(this).addClass('active').siblings().removeClass('active');
		})
})
$('.maxTabsnew li').each(function(i){$(this).hover(function(){
		$(this).addClass('current').siblings().removeClass('current');
		$(this).parents('.itemnew').find('.maxTabsnew-body').eq(i).addClass('cur').siblings().removeClass('cur');
		
		})
			})
$('.f3 .maxTabs li').each(function(i){$(this).hover(function(){
		$(this).addClass('current').siblings().removeClass('current');
		$(this).parents('.f3').find('.maxTabs-body').eq(i).addClass('cur').siblings().removeClass('cur');
		
		})
			})

	})