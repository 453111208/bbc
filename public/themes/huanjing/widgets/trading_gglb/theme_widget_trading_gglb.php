<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_trading_gglb(&$setting)
{
    	$sprodreleaseSql = "SELECT
				sni.*, snt.type_name
			FROM
				sysnotice_notice_item sni
			LEFT JOIN sysnotice_notice_type snt ON sni.type_id = snt.type_id
			ORDER BY notice_time DESC";
	$sprodreleaseList = app::get("base")->database()->executeQuery($sprodreleaseSql)->fetchAll();
	foreach ($sprodreleaseList as $key => $value) {
		$type=$value["type_name"];
		if ($type == "招标"){
		        $sprodreleaseList[$key]["notice_type_name"]="maxpic-zb";
		} else {
		         $sprodreleaseList[$key]["notice_type_name"]="maxpic";
		}
	}
	$setting["sprodreleaseList"] = $sprodreleaseList;
	return $setting;
}

?>
