<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_pubilc_gxtj(&$setting)
{
	foreach($setting['require_select'] as $key=>$value)
	{
	    $require_id =$value;
	    $artList = app::get("sysspfb")->model("requireInfo")->getList("*",array('require_id'=>$require_id));
	    $_return['require_list'][$key]["require_id"]=$value;
	    $_return['require_list'][$key]["variety_name"]=$artList[0]['variety_name'];
	}

	foreach($setting['supply_select'] as $key=>$value)
	{
	    $supply_id =$value;
	    $artList = app::get("sysspfb")->model("supplyInfo")->getList("*",array('supply_id'=>$supply_id));
	    $_return['supply_list'][$key]["supply_id"]=$value;
	    $_return['supply_list'][$key]["variety_name"]=$artList[0]['variety_name'];
	}
    	return $_return;
}

?>
