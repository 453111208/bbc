$(document).ready(function() {
    $('.maxhd .max-tabs-body .maxcomon').each(function(mun) {
        $(this).find('ul').carouFredSel({
            //auto: true,
            prev: $('.max-tabs-body .maxcomon').eq(mun).find('.prev'),
            next: $('.max-tabs-body .maxcomon').eq(mun).find('.next'),
            width: 780,
            items: 4,
            mousewheel: true,
            auto: false,
            swipe: {
                onMouse: true,
                onTouch: true
            }
        });
    });
    $('.maxpr-solid  .max-tabs-top').find('li').each(function(i) {
        $(this).closest('.maxpr-solid').find('.maxcomon').eq(0).css('display', 'block').siblings().hide()
        $(this).closest('.maxpr-solid').find('.maxtext').eq(0).css('display', 'block');
        $(this).hover(function() {
                $(this).addClass('active').siblings().removeClass('active');
                //
                if ($('.maxpr-solid  .max-tabs-body')) {
                    //
                    // console.log(i);
                    //$(this).parents('.maxpr-solid').find('.maxcomon').eq(i).css('display','block').siblings().hide();
                    if (i == 1 || i == 0) {
                        $(this).parents('.maxpr-solid').find('.maxcomon').eq(i).css('display', 'block').siblings().hide();
                    } else if (i == 2) {
                        if ($(this).parents('.maxpr-solid').find('.max-tabs-top').find("li").eq(0).text() == "固废") {
                        $(this).parents('.maxpr-solid').find('.maxcomon').eq(0).css('display', 'block').siblings().hide();
                    } else {
                        $(this).parents('.maxpr-solid').find('.maxcomon').eq(i).css('display', 'block').siblings().hide();
                    }
                } else {
                    if ($(this).parents('.maxpr-solid').find('.max-tabs-top').find("li").eq(0).text() == "固废") {
                    $(this).parents('.maxpr-solid').find('.maxcomon').eq(1).css('display', 'block').siblings().hide();
                } else {
                    $(this).parents('.maxpr-solid').find('.maxcomon').eq(i).css('display', 'block').siblings().hide();
                }
            }
        }
        if ($('.maxpr-solid  .maxtext')) {
            $(this).parents('.maxpr-solid').find('.maxtext').css('display', 'none');
            $(this).parents('.maxpr-solid').find('.maxtext').eq(i).css('display', 'block');
        }
    })
})
$(".munu-list li a").each(function() {
$this = $(this);
if ($this[0].href == String(window.location)) {
    $this.parent().addClass("current");
}
})
})