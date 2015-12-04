<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_trading_zxjj(&$setting)
{
    	$sprodreleaseSql = "SELECT
				sbid.bidding_id, sbid.shop_id, sbid.shop_name, sbid.trading_title, ssp.shop_area, ssi.net_price,sbid.stop_time,
				GROUP_CONCAT(ssi.title) 'title', GROUP_CONCAT(ssi.num) 'num', GROUP_CONCAT(ssi.unit) 'unit', GROUP_CONCAT(sit.image_default_id) 'img_id', tpt.mbid, tpt.cbid
			FROM
				sysshoppubt_biddings sbid
			LEFT JOIN sysshop_shop ssp ON sbid.shop_id = ssp.shop_id
			LEFT JOIN sysshoppubt_standard_item ssi ON sbid.uniqid = ssi.uniqid
			LEFT JOIN sysitem_item sit ON ssi.item_id = sit.item_id
			LEFT JOIN (SELECT
				a.bidding_id, b.mbid, c.cbid
			FROM
				sysshoppubt_tradeorder a
			INNER JOIN (
				SELECT
					bidding_id, MAX(totalbid) AS mbid
				FROM
					sysshoppubt_tradeorder
				GROUP BY
					bidding_id
			) b ON a.bidding_id = b.bidding_id
			INNER JOIN (
				SELECT
					bidding_id, COUNT(bidding_id) AS cbid
				FROM
					sysshoppubt_tradeorder
				GROUP BY
					bidding_id
			) c ON a.bidding_id = c.bidding_id
			AND a.totalbid = b.mbid) tpt ON tpt.bidding_id = sbid.bidding_id
			WHERE
				sbid.is_through = 1 AND sbid.isok IN (0,2) AND sbid.start_time < unix_timestamp(now()) AND sbid.stop_time > unix_timestamp(now())
			GROUP BY 
			  sbid.bidding_id, sbid.shop_id, sbid.shop_name, sbid.trading_title, ssp.shop_area, ssi.net_price,sbid.stop_time, tpt.mbid, tpt.cbid ORDER BY sbid.create_time DESC LIMIT 5";
	$sprodreleaseList = app::get("base")->database()->executeQuery($sprodreleaseSql)->fetchAll();
	foreach ($sprodreleaseList as $key => $value) {
		$img=$value["img_id"];
		$imgarr=explode(",", $img);
		$sprodreleaseList[$key]["img_id"]=$imgarr[0];
		$unit=$value["unit"];
		$unitarr=explode(",", $unit);
		$sprodreleaseList[$key]["unit"]=$unitarr[0];
		$interval_time = $value["stop_time"]-time();
       		$sprodreleaseList[$key]["interval_time"] = $interval_time;
	}
	$setting["sprodreleaseList"] = $sprodreleaseList;
	//var_dump($sprodreleaseList);
	return $setting;
}

?>
