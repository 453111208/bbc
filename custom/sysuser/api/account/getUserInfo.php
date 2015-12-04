<?php 
class sysuser_api_account_getUserInfo {
	public function getParams()
    {
        $return['params'] = array(
            'shop_id' => ['type'=>'int','valid'=>'required','description'=>'店铺ID','default'=>'','example'=>'1'],
       );

        return $return;
    }
    //根据shop_id查询用户信息
	function getUserInfo($params){
		$shopInfo = app::get("sysshop")->model("shop")->getRow("*",array("shop_id"=>$params['shop_id']));
		$sellerInfo=app::get("sysshop")->model("account")->getRow("*",array("seller_id"=>$shopInfo["seller_id"]));
		$userInfo=app::get("sysuser")->model("account")->getRow("*",array("login_account"=>$sellerInfo['login_account']));
	    
	    return $userInfo;
	}
     
}

 ?>