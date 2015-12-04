<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class topc_ctl_require extends topc_controller {

	public function index()
	{
	$requireId = intval(input::get('require_id'));# code...
	$type=intval(input::get('type'));# code...
	if( empty($requireId) )
        	{
            		return redirect::action('topc_ctl_default@index');
        	}
        	if( userAuth::check() )
        	{
           		$pagedata['nologin'] = 1;
        	}
        	$requireItem= app::get("sysspfb")->model("requireInfo")->getRow("*",array("require_id"=>$requireId ));
          $list_image2=array();
          
          //判断当前供求信息是否为当前登录人发布的
            $userId = userAuth::id();
            if($userId == $requireItem["user_id"]){
                $pagedata['myreqsupp'] = 1;
            }
          
            $list_image=explode(",",$requireItem["list_image"]);

            foreach ($list_image as $key => $value) {
                if($key<count($list_image)-1){

                array_push($list_image2, $value);
                
                }
            }
             $requireItem['list_image']=$list_image2;
             $userid=$requireItem["user_id"];
            $params["user_id"]=$userid;
            $shopInfo=app::get('topc')->rpcCall('shop.get.shopInfo',$params,'buyer');
        	$pagedata["requireItem"]=$requireItem;
        	$pagedata["type"] = $type;
            $pagedata["shopinfo"]=$shopInfo;

            //查询是否有询价信息
            $enquireinfolist = app::get("sysspfb")->model("enquireinfo")->getList("*",array("reqsupp_id"=>$requireId,"ifrequire"=>2,"user_id"=>$userId));
            $pagedata["enquireinfolist"] = $enquireinfolist;

             $this->setLayoutFlag('supply');

        	return $this->page('topc/items/index.html', $pagedata);

	}
}
