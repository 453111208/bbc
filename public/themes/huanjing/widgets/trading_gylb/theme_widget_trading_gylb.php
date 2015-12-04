<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_trading_gylb(&$setting)
{
    	$sprodreleaseSql = "SELECT ss.* FROM sysspfb_supplyInfo ss ORDER BY create_time DESC LIMIT 5";
	$sprodreleaseList = app::get("base")->database()->executeQuery($sprodreleaseSql)->fetchAll();
	$_return["sprodreleaseList"] = $sprodreleaseList;
	foreach($setting['info_select'] as $key=>$value)
	{
	    $supply_id =$value;
	    $artList = app::get("sysspfb")->model("supplyInfo")->getList("*",array('supply_id'=>$supply_id));
	    $_return['info_select'][$key]["supply_id"]=$value;
	    $_return['info_select'][$key]["variety_name"]=$artList[0]['variety_name'];
	}
    return $_return;
}

?>
