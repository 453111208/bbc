function list(){
   $(".list").css({"position":"relative"});
   $(".caret").css({"border-left":"5px solid","border-bottom": "5px solid transparent","border-top":"5px solid transparent"});
   $(".list ul").css({"display":"none","list-style":"none","position":"absolute","left":"11em","border":"1em solid rgba(255,255,255,0)","top":"-1em"}).children("li").css({"line-height":"2","text-align":"center"});
   $(".list .nav1").mouseover(
     function(){   
       $(this).children("ul").css("display","block").children("li").css("display","block");
     }
   );
   $(".list .nav1").mouseout(
     function(){
       $(this).children("ul").css("display","none").children("li").css("display","none");
     }
   );
   $(".list .nav2>li").mouseover(
     function(){
       $(this).css("color","#35D").children("ul").css("display","block").parent().siblings().css("color","#ccc").children("ul").css("display","none");
     }
   );
}