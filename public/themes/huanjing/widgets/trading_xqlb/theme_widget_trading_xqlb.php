<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_trading_xqlb(&$setting)
{
    	$sprodreleaseSql = "SELECT ss.* FROM sysspfb_requireInfo ss ORDER BY create_time DESC LIMIT 5";
	$sprodreleaseList = app::get("base")->database()->executeQuery($sprodreleaseSql)->fetchAll();
	$_return["sprodreleaseList"] = $sprodreleaseList;
	foreach($setting['info_select'] as $key=>$value)
	{
	    $require_id =$value;
	    $artList = app::get("sysspfb")->model("requireInfo")->getList("*",array('require_id'=>$require_id));
	    $_return['info_select'][$key]["require_id"]=$value;
	    $_return['info_select'][$key]["variety_name"]=$artList[0]['variety_name'];
	}
    return $_return;
}

?>
