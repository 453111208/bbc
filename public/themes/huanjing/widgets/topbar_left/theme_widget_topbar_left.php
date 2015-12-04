
<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_topbar_left(&$setting)
{

	$UNAME=$_COOKIE['UNAME'];
	$seller=app::get("sysshop")->model("account")->getRow("*",array("login_account"=>$UNAME));
	$sellerinfo=app::get("sysshop")->model("seller")->getRow("*",array("seller_id"=>$seller["seller_id"]));
	$data['UNAME']=$sellerinfo["name"];
	
	//$data['UNAME']=$UNAME;
    	return $data;
}
?>
