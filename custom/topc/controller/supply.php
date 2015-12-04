<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topc_ctl_supply extends topc_controller {

	public function index()
	{
	$supplyId = intval(input::get('supply_id'));# code...
	$type=intval(input::get('type'));# code...
	if( empty($supplyId) )
        	{
            	return redirect::action('topc_ctl_default@index');
        	}
        	if( userAuth::check() )
        	{
           	$pagedata['nologin'] = 1;
        	}
        	$supplyItem= app::get("sysspfb")->model("supplyInfo")->getRow("*",array("supply_id"=>$supplyId ));
            $list_image2=array();
            
            //判断当前供求信息是否为当前登录人发布的
            $userId = userAuth::id();
            if($userId == $supplyItem["user_id"]){
                $pagedata['myreqsupp'] = 1;
            }

            $list_image=explode(",",$supplyItem["list_image"]);

            foreach ($list_image as $key => $value) {
                if($key<count($list_image)-1){

                array_push($list_image2, $value);
                
                }
            }
            //var_dump($list_image2);
            $supplyItem['list_image']=$list_image2;
    
            //企业信息
            $userid=$supplyItem["user_id"];
            $params["user_id"]=$userid;
            $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
        	$pagedata["requireItem"]=$supplyItem;
        	$pagedata["type"] = $type;
            $pagedata["shopinfo"]=$shopInfo;
            
            //查询是否有询价信息
            $enquireinfolist = app::get("sysspfb")->model("enquireinfo")->getList("*",array("reqsupp_id"=>$supplyId,"ifrequire"=>1,"user_id"=>$userId));
            $pagedata["enquireinfolist"] = $enquireinfolist;
            
            $this->setLayoutFlag('supply');
          
         //   var_dump( $type);
        	return $this->page('topc/items/index.html', $pagedata);

	}
}
