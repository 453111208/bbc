<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_main_shoplist(&$setting)
{
    	$sprodreleaseSql = "SELECT ss.* FROM sysshop_shop ss";
	$sprodreleaseList = app::get("base")->database()->executeQuery($sprodreleaseSql)->fetchAll();
	$setting["sprodreleaseList"] = $sprodreleaseList;
	//var_dump($sprodreleaseList);
	return $setting;
}

?>
