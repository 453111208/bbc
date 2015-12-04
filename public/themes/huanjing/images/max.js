//��ҳ�����Ӳ˵�����
$('.maxsubmenu li').hover(function() {
    $(this).addClass('hover')

},
function() {
    $(this).removeClass('hover')

});

//��൯���˵�
$('.category-primary > li').each(function(e) {

    $(this).hover(function() {

        $(this).addClass('current')

    },
    function() {
        $(this).removeClass('current')

    }

    );

});

//��൯���˵�
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
			$('.maxTopw .maxLogo').css("margin-top","-17px");
				

           
        }else if (targetTop <155){
            $('.maxTopw').removeClass('fixed');
            $('.maxTopw .maxLogo').css("margin-top","0");
            }
    })

// $("a").click(function(event) {
//     /* Act on the event */
//     if($(this).attr("href")==""||$(this).attr("href")=="#"||$(this).attr("href")=null){
//         return false;
//     }
// });