//首页顶部子菜单下拉
$('.maxsubmenu li').hover(function() {
    $(this).addClass('hover')

},
function() {
    $(this).removeClass('hover')

});

//左侧弹出菜单
$('.category-primary > li').each(function(e) {

    $(this).hover(function() {

        $(this).addClass('current')

    },
    function() {
        $(this).removeClass('current')

    }

    );

});

//左侧弹出菜单
$('.maxkf .item').each(function(e) {

    $(this).hover(function() {

        $(this).addClass('current')

    },
    function() {
        $(this).removeClass('current')

    }

    );
if ($(this).hasClass('item4'))
{$(this).click(function(){  
                $('body,html').animate({scrollTop:0},1000);  
                return false;  
            });  
}
});


    $(window).scroll(function(){
		var targetTop = $(this).scrollTop();

        if(targetTop >155){
			$('.maxTopw').addClass('fixed')
				// $('.maxTopw .maxLogo img').attr('src','images/logo2.png')
				

           
        }else if (targetTop <155){$('.maxTopw').removeClass('fixed');
       // $('.maxTopw .maxLogo img').attr('src','images/logo.jpg')
    }
    })
