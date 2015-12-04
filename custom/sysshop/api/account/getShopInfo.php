<?php 
class sysshop_api_account_getShopInfo {
	public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int','valid'=>'required','description'=>'会员ID','default'=>'','example'=>'1'],
       );

        return $return;
    }
	function getShopInfo($params){
		 $userInfo=app::get("sysuser")->model("account")->getRow("*",array("user_id"=>$params['user_id']));
	     $loginAccount = $userInfo["login_account"];
	     $sellerInfo=app::get("sysshop")->model("account")->getList("*",array("login_account"=>$loginAccount));
	     $sellerId = $sellerInfo[0]["seller_id"];
	     $shopInfoList = app::get("sysshop")->model("shop")->getlist("*",array("seller_id"=>$sellerId));
		 //$shopInfo=$shopInfoList[0];
	     return $shopInfoList[0];

	}
	 //$userId =  userAuth::id();
     
}

 ?>