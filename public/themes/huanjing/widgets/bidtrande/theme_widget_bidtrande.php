<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_bidtrande(&$setting)
{
  $now=time();
  $biddingSql = "select * from sysshoppubt_biddings where  is_through=1 and isok<>1 and start_time<".$now." and stop_time >".$now."  order by create_time desc LIMIT 2";
   $list = app::get("base")->database()->executeQuery($biddingSql)->fetchAll();
    foreach ($list as $key=>$item) {
   	# code...
   	$price = 0;
   	$id=$item["bidding_id"];
    //当前价格
   	$sql = "select totalbid from sysshoppubt_tradeorder where bidding_id=".$id."  order by totalbid desc LIMIT 1";
   	$biddingList = app::get("base")->database()->executeQuery($sql)->fetchAll();
   	$a = $biddingList[0];
   	$list[$key]["b_price"] = $biddingList[0]["totalbid"];
    //低价
    $djSql = "select sum(net_price*num) dj from sysshoppubt_standard_item a
                    left join sysshoppubt_biddings b
                    on a.uniqid=b.uniqid
                    where b.bidding_id=".$id."";
    $dj = app::get("base")->database()->executeQuery($djSql)->fetchAll();
    $list[$key]["dj"] =$dj[0]["dj"];
    //出价次数
    $cjSql = "select count(*) count from sysshoppubt_tradeorder where bidding_id is not NULL and bidding_id = ".$id."";
    $cj = app::get("base")->database()->executeQuery($cjSql)->fetchAll();
    $list[$key]["cjcount"] =$cj[0]["count"];
    //图片
    $imgSql = "select image_default_id from sysitem_item a
                      left join sysshoppubt_standard_item b
                      on a.item_id = b.item_id
                      left join sysshoppubt_biddings c
                      on b.uniqid = c.uniqid 
                      where c.bidding_id=".$id."";
      $imgList =  app::get("base")->database()->executeQuery($imgSql)->fetchAll();
       $list[$key]["imgDefalut"] = $imgList[0]["image_default_id"] ;
      $list[$key]["imgList"] =$imgList;
      //交割地址
      $jgSql = "select area from sysshoppubt_deliveryaddr a
                     left join sysshoppubt_biddings b
                    on a.uniqid = b.uniqid
                    where b.bidding_id = ".$id." and a.def_addr=1";
      $jqList = app::get("base")->database()->executeQuery($jgSql)->fetchAll();
      $list[$key]["jgdz"] =$jqList[0]["area"];
      $time=$now-$item["start_time"];
      $list[$key]["lasttime"]=$time;
   }
   $setting["list"] = $list;
   //招标
    $trenderSql = "select * from sysshoppubt_tender where  is_through=1 and isok<>1 and start_time<".$now." and stop_time >".$now." order by create_time desc LIMIT 3";
    $list2 = app::get("base")->database()->executeQuery($trenderSql)->fetchAll();
    foreach ($list2 as $key=>$item) {
      $id=$item["tender_id"];
      $zbimgSql = "select image_default_id from sysitem_item a
                      left join sysshoppubt_standard_item b
                      on a.item_id = b.item_id
                      left join sysshoppubt_tender c
                      on b.uniqid = c.uniqid 
                      where c.tender_id=".$id."";
      $zbimgList =  app::get("base")->database()->executeQuery($zbimgSql)->fetchAll();
       $list2[$key]["imgDefalut"] = $imgList[0]["image_default_id"] ;
      $list2[$key]["imgList"] =$imgList;
      $time2=$now-$item["start_time"];
      $list2[$key]["lasttime"]=$time2;
    }
    
    $setting["listTender"] = $list2;
    
      
      
    return $setting;


}

?>
