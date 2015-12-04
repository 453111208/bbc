<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_trading_zxzb(&$setting)
{
    	$sprodreleaseSql = "SELECT
				std.tender_id, std.shop_id, ssp.shop_name, std.trading_title, ssp.shop_area,std.stop_time, GROUP_CONCAT(sit.image_default_id) 'img_id'
			FROM
				sysshoppubt_tender std
			LEFT JOIN sysshop_shop ssp ON std.shop_id = ssp.shop_id
			LEFT JOIN sysitem_item sit ON std.shop_id = sit.shop_id
			WHERE
				is_through = 1 AND std.isok IN (0,2) AND std.start_time < unix_timestamp(now()) AND std.stop_time > unix_timestamp(now())
			GROUP BY
				std.tender_id, std.shop_id, std.shop_name, std.trading_title, ssp.shop_area,std.stop_time ORDER BY std.create_time DESC LIMIT 5";
	$sprodreleaseList = app::get("base")->database()->executeQuery($sprodreleaseSql)->fetchAll();
	foreach ($sprodreleaseList as $key => $value) {
		$img=$value["img_id"];
		$imgarr=explode(",", $img);
		$sprodreleaseList[$key]["img_id"]=$imgarr[0];
		$interval_time = $value["stop_time"]-time();
       		$sprodreleaseList[$key]["interval_time"] = $interval_time;
	}
	$setting["sprodreleaseList"] = $sprodreleaseList;
	//var_dump($sprodreleaseList);
	return $setting;
}

?>
