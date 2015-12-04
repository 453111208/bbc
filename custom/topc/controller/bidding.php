<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topc_ctl_bidding extends topc_controller {

	public function index(){
		$bidddingId = intval(input::get('bidding_id'));# code...
  $this->bidding_model=app::get('sysshoppubt')->model('biddings');
      $comment = app::get('sysshoppubt')->model('comment');
		if( empty($bidddingId) )
        	{
            	return redirect::action('topc_ctl_default@index');
        	}
        	 	if(userAuth::check())
        	{
           	$pagedata['nologin'] = 1;
        	}
          //交易详情信息
        	$biddingItem= app::get("sysshoppubt")->model("biddings")->getRow("*",array("bidding_id"=>$bidddingId ));
        	$tradeorder= app::get("sysshoppubt")->model("tradeorder")->getRow("*",array("bidding_id"=>$bidddingId,'state'=>1));
          //企业信息
          $shopId=$biddingItem["shop_id"];
          $shopinfo=app::get("sysshop")->model("shop")->getRow("*",array("shop_id"=>$shopId));
          //其他竞价
          $otherSql="select a.bidding_id,a.trading_title,max(c.image_default_id) image_default_id from sysshoppubt_biddings a LEFT JOIN sysshoppubt_standard_item b
                      on a.uniqid=b.uniqid
                      left join sysitem_item c on b.item_id=c.item_id
                      WHERE a.bidding_id<>".$bidddingId."
                      group by 1,2";
          $otherList=app::get("base")->database()->executeQuery($otherSql)->fetchAll();
          $pagedata["shopinfo"]=$shopinfo;
          $pagedata['otherList']=$otherList;
          $tradSql = "select tradeorder_id,totalbid from sysshoppubt_tradeorder where bidding_id=".$bidddingId."  order by totalbid desc";
        	
          $tradeorderListall =app::get("base")->database()->executeQuery($tradSql)->fetchAll();
          $pagedata["tendercount"]=count($tradeorderListall);
          $tradeorderList=array_slice($tradeorderListall,0,1);
          if($tradeorderList ){
              $pagedata["b_price_type"]="1";
              $pagedata["totalbid"]=$tradeorderList[0]["totalbid"];
              $tradeorder_id=$tradeorderList[0]["tradeorder_id"];
              $itemSql = "select a.*,c.bid,d.image_default_id from sysshoppubt_standard_item a 
                          left join sysshoppubt_biddings b on a.uniqid = b.uniqid 
                          left join (select item_id,bid from sysshoppubt_torderitem where tradeorder_id = ". $tradeorder_id.") c on a.item_id=c.item_id
                          left join sysitem_item d on a.item_id=d.item_id     
                                where b.bidding_id=".$bidddingId."";
              $biddingItemList=app::get("base")->database()->executeQuery($itemSql)->fetchAll();
              $pagedata["itemList"] = $biddingItemList;
          }
          else{
             $pagedata["b_price_type"]="0";
             $pagedata["totalbid"]=0;
             $biddingItemList = app::get("sysshoppubt")->model("standard_item")->getList("*",array("uniqid"=>$biddingItem['uniqid'] ));
             foreach ($biddingItemList as $key => $value) {
                $biddingItemList[$key]["bid"]=0;
                $itemid=$value["item_id"];
                $itemRow=app::get('sysitem')->model('item')->getRow("*",array("item_id"=>$itemid));
                $biddingItemList[$key]["image_default_id"]=$itemRow["image_default_id"];
                $prop=app::get('syscategory')->model('item_prop_value')->getList("*",array("item_id"=>$itemid));
                $biddingItemList[$key]["prop"]=$prop;
               # code...
             }
            $pagedata["itemList"] = $biddingItemList;
          }
          $pagedata["requireItem"]=$biddingItem;
          $pagedata['tradeorder'] = $tradeorder;
          //弹出层
        	 $article_id = "1";
           $artList = app::get("syscontent")->model("article")->getList("*",array('article_id'=>$article_id));
           $pagedata["artList"]=$artList[0]['content'];
            
            $article_id = "2";
            $artList = app::get("syscontent")->model("article")->getList("*",array('article_id'=>$article_id));
            $pagedata["dialogPrice"]=$artList[0]['content'];
            //详细页右侧信息
            $tradSqlPm = "select user_name,tradeorder_id,totalbid from sysshoppubt_tradeorder where bidding_id=".$bidddingId."  order by totalbid desc LIMIT 4";
            $pmList =app::get("base")->database()->executeQuery($tradSqlPm)->fetchAll();
            $pagedata["pm"] = $pmList;
            //看样
          $bidding=$this->bidding_model->getRow('*',array('bidding_id'=>$bidddingId));
          $pagedata['rows'] = $bidding;
          $pagedata['type'] = 1;
          if($bidding['seegoods_stime']<time()&&$bidding['seegoods_stime']!=null){
        $pagedata['sample_end']='1'; 
        }elseif($bidding['seegoods_stime']==null){
        $pagedata['sample_end']='0';
        }else{
        $pagedata['sample_end']='2';
        }
        $starttime = $bidding['start_time']-time();
    $stoptime = $bidding['stop_time']-time();
    if($bidding['start_time']>time()&&$bidding['isok']!=1){
      $pagedata['state'] = 0;
      $pagedata['totaltime'] = $starttime;
    }elseif($bidding['stop_time']>time()&&$bidding['isok']!=1){
      $pagedata['state'] = 1;
      $pagedata['totaltime'] = $stoptime;
    }else{
      $pagedata['state'] = 2;
      $pagedata['totaltime'] = 0;
    }
    $commentnum = $comment->count(array('shop_id'=>$shopId,'item_id'=>$bidddingId));
    $pagedata['commentnum'] = $commentnum;
      $this->setLayoutFlag('bidding');
      return $this->page('topc/bidding/index.html', $pagedata);
	}

  public function sendPrice()
  {
    # code...
    $postdata = $_POST;
    $biddingId = $postdata["bidding_id"];
    $shop_id=$postdata["shop_id"];
    $zybidding = app::get("sysshoppubt")->model("biddings");
    $shopInfo = app::get("sysshop")->model("shop")->getRow("*",array("shop_id"=>$shop_id));
    $biddingInfo = $zybidding->getRow("*",array("bidding_id"=>$biddingId));
    $postdata["title"]=$biddingInfo["trading_title"];
    $postdata["shop_name"] = $shopInfo["shop_name"];
    $userId = userAuth::id();

    if(!$userId){
       return $this->splash('error',null,"请先登录");
    }
    if($biddingInfo['is_through']!=1){
      return $this->splash('error',null,"该交易暂未通过审核，出价无效");
    }
    if($biddingInfo['start_time']>time()){
      return $this->splash('error',null,"竞价暂未开始");
    }elseif ($biddingInfo['stop_time']<time()) {
      return $this->splash('error',null,"竞价已经结束");
    }
    $params["user_id"]=$userId;
    $shopInfoGet=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
    if(!$shopInfoGet){
       return $this->splash('error',null,"您的账号类型为普通会员，请使用企业账号参与交易！");
    }
    $postdata["user_id"] = $shopInfoGet["shop_id"];
    if($shop_id==$shopInfoGet["shop_id"]){
      return $this->splash('error',null,"不可参与自己发布的竞价");
    }else{
      $check = app::get("sysshoppubt")->model("tradeorder")->getRow('*',array('state'=>1,'bidding_id'=>$biddingId));
      if(!$check){
    $postdata["user_name"] = $shopInfoGet["shop_name"];
    $postdata["fixed_price"] = $biddingInfo["fixed_price"];
    $postdata["type"] = 1;
    $postdata["create_time"] = time();
    $ensurence = $postdata["ensurence"];
    $userInfo = app::get("sysuser")->model("user")->getRow("*",array("user_id"=>$userId));
    $hjadvance = $userInfo["hjadvance"];
   try {
      $moneyrecoder = app::get("sysshoppubt")->model("moneyrecoder");
      $moneyrecoders = $moneyrecoder->getRow('*',array('user_id'=>$shopInfoGet['shop_id'],'item_id'=>$biddingId,'type'=>1));
      if(!$moneyrecoders){
      if(floatval($hjadvance)>=floatval($ensurence)){
      $result = floatval($hjadvance)-floatval($ensurence);
    $sql = "UPDATE sysuser_user set hjadvance=". $result ." where user_id=".$userId;
     app::get('sysuser')->database()->executeUpdate($sql);
     $moneyreco = array(
        "user_id"=>$userId,
        "changemoney" =>$ensurence,
        "name"=>$shopInfoGet["shop_name"],
        "types"=>0,
        "username"=>userAuth::getLoginName(),
        "pay"=>2,
        "bidding_id"=>$biddingId,
        "create_time"=>time()
      );
     app::get("sysuser")->model("moneyreco")->save($moneyreco);
     $str = array(
        "item_id"=>$biddingId,
        "shop_id" =>$biddingInfo['shop_id'],
        "shop_name"=>$biddingInfo["shop_name"],
        "type"=>1,
        "user_id"=>$shopInfoGet['shop_id'],
        "money"=>$ensurence,
        "create_time"=>time()
      );
     $moneyrecoder->save($str);
    }
    else {
       return $this->splash('error',null,"您的预存款不足");
    }
  }
    } catch (Exception $e) {
      $msg = $e->getMessage();
      return $this->splash('error',null,$msg);
    }
    //存出价订单信息
    $postdata['shop_id']  = $_POST["shop_id"];
    $postdata["shop_name"] =$shopInfo["shop_name"];
    $fixprice = app::get("sysshoppubt")->model("biddings")->getRow('fixed_price',array('bidding_id'=>$biddingId));
      if($postdata["totalbid"]>$fixprice['fixed_price']){
        $postdata["totalbid"] = $fixprice['fixed_price'];
        $postdata["state"] = 1;
        $getall = $zybidding->getRow('*',array('bidding_id'=>$biddingId));
        $getallold = $getall;
        $getall['isok'] = 1;
        try{
        $zybidding->update($getall,$getallold);
        } catch (Exception $e) {
          $msg = $e->getMessage();
          return $this->splash('error',null,$msg);
        }
    }
    try {
    app::get("sysshoppubt")->model("tradeorder")->save($postdata);
    } catch (Exception $e) {
          $msg = $e->getMessage();
          return $this->splash('error',null,$msg);
    }
      return $this->splash('success',null,"竞价完成");
    }else{
      return $this->splash('error',null,"该项交易已经结束");
      }
    }
  }

  public function once(){
    $userid = userAuth::id();
    $item = app::get('sysitem')->model('item');
    $standartitem = app::get('sysshoppubt')->model('standard_item');
    $bidding=app::get('sysshoppubt')->model('biddings');
    $user = app::get('sysuser')->model('user');
    $tradeorder = app::get('sysshoppubt')->model('tradeorder');
    $data = input::get('bidding_id');
    $biddings = $bidding->getRow('*',array('bidding_id'=>$data));
    $params["user_id"]=$userid;
    $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
    $shop_id=$shopInfo['shop_id'];
    if($biddings['start_time']>time()){
      return 5;
    }elseif($biddings['stop_time']<time()){
      return 4;
    }
    if($shop_id == $biddings['shop_id']){
      return 0;
    }elseif($shop_id){
      $check = $tradeorder->getRow('*',array('state'=>1,'bidding_id'=>$data));
      if(!$check){
      $userinfo = $user->getRow('*',array('user_id'=>$userid));
      if($userinfo['hjadvance']>=$biddings['ensurence']){
      $oldinfo = $userinfo;
      $userinfo['hjadvance'] -= intval($biddings['ensurence']);
      try {
        $user->update($userinfo,$oldinfo);
        
      } catch (Exception $e) {
      
        $msg = $e->getMessage();
        return $msg;
      }
      $oldbiddings = $biddings;
      $biddings['isok'] = 1;
      try {
        $bidding->update($biddings,$oldbiddings);
      } catch (Exception $e) {
        $msg = $e->getMessage();
        return $msg;
      }
      $arr['bidding_id'] = $data;
      $arr['title'] = $biddings['trading_title'];
      $arr['shop_id'] = $biddings['shop_id'];
      $arr['shop_name'] = $biddings['shop_name'];
      $arr['user_id'] = $shopInfo["shop_id"];
      $arr['user_name'] = $shopInfo["shop_name"];
      $arr['fixed_price'] = $biddings['fixed_price'];
      $arr['totalbid'] = $biddings['fixed_price'];
      $arr['type'] = 1;
      $arr['state'] = 1;
      $arr['create_time'] = time();
      try {
      $tradeorder->save($arr);
      } catch (Exception $e) {
         $msg = $e->getMessage();
        return $msg;
      }
      $notice = app::get('sysnotice')->model('notice_item');
      $tenderinfo = $bidding->getRow('*',array('bidding_id'=>$data));
        $itemid = $standartitem->getRow('item_id',array('uniqid'=>$tenderinfo['uniqid']));
        $itemimg = $item->getRow('*',array('item_id'=>$itemid['item_id']));
        $img = split(',', $itemimg['list_image']);
        $arr['notice_name'] = $biddings['trading_title'];
        $arr['notice_content'] = "用户".$userid."以一口价".$biddings['fixed_price']."赢得".$biddings['shop_name']."的".$biddings['trading_title']."竞价";
        $arr['notice_time'] = time();
        $arr['type_id'] = "竞价";
        $arr['image_default_id'] = $img[0];
        try{
        $notice->save($arr);
        }catch(Exception $e){
        $msg = $e->getMessage();
        return $msg;
        }
      return 1;
    }else{
      return 2;
    }
  }else{
    return 4;
  }
    }else{
      return 3;
    }
  }

  public function wish(){
    $data = input::get();
    $userid = userAuth::id();
    $shop = app::get('sysshop')->model('shop');
    $shopinfo = $shop->getRow('*',array('shop_id'=>$data['shop_id']));
    $params["user_id"]=$userid;
    $shopInfoGet=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
    if($shopInfoGet['shop_id']!=$data['shop_id']){
    $arr['bidding_id'] = $data['bidding_id'];
    $arr['user_id'] = $shopInfoGet['shop_id'];
    $arr['user_name'] = $shopInfoGet['shop_name'];
    $arr['shop_id'] = $data['shop_id'];
    $arr['shop_name'] = $shopinfo['shop_name'];
    $arr['state'] = 3;
    $arr['create_time'] = time();
    $arr['title'] = $data['title'];
    $tradeorder = app::get('sysshoppubt')->model('tradeorder');
    try {
    $tradeorder->save($arr);
    } catch (Exception $e) {
      $msg = $e->getMessage();
      return $msg;
    }
  }
    return 1;
  }
}