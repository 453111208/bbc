<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_expert_pic(&$setting){

	foreach($setting['expert_select'] as $key=>$value)
	{
	    $expert_id =$value;
	    $artList = app::get("sysexpert")->model("expert")->getList("*",array('expert_id'=>$expert_id));
	    $_return['expertList'][$key]["expert_id"]=$value;
	    $_return['expertList'][$key]["name"]=$artList[0]['name'];
	    $_return['expertList'][$key]["nickname"]=$artList[0]['nickname'];
	    $_return['expertList'][$key]["image_logo"]=$artList[0]['image_logo'];
	    $_return['expertList'][$key]["summary"]=$artList[0]['summary'];
	}
    return $_return;
}
?>
