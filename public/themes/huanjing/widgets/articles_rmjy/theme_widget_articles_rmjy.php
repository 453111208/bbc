<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_articles_rmjy(&$setting)
{
    $biddingids = "";
    foreach ($setting['biddingList'] as $key => $value) {
    	$biddingids.=",".$value;
    }
    $biddingids = substr($biddingids,1);
    if($biddingids){
    $sprodreleaseSql = "SELECT
			sbid.bidding_id, sbid.shop_id, sbid.shop_name, sbid.trading_title, ssp.shop_area, GROUP_CONCAT(ssi.net_price) 'net_price', '竞价' AS transaction_type,
			GROUP_CONCAT(ssi.title) 'title', GROUP_CONCAT(ssi.num) 'num', GROUP_CONCAT(ssi.unit) 'unit', GROUP_CONCAT(sit.image_default_id) 'img_id',
			tpt.mbid, ifnull(tpt.transaction_num,0) AS transaction_num
		FROM
			sysshoppubt_biddings sbid
		LEFT JOIN sysshop_shop ssp ON sbid.shop_id = ssp.shop_id
		LEFT JOIN sysshoppubt_standard_item ssi ON sbid.uniqid = ssi.uniqid
		LEFT JOIN sysitem_item sit ON ssi.item_id = sit.item_id
		LEFT JOIN (SELECT
			a.bidding_id, b.mbid, c.transaction_num
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
				bidding_id, COUNT(bidding_id) AS transaction_num
			FROM
				sysshoppubt_tradeorder
			GROUP BY
				bidding_id
		) c ON a.bidding_id = c.bidding_id
		AND a.totalbid = b.mbid) tpt ON tpt.bidding_id = sbid.bidding_id
		WHERE
			sbid.is_through = 1 AND sbid.bidding_id IN (".$biddingids.")
		GROUP BY 
    sbid.bidding_id, sbid.shop_id, sbid.shop_name, sbid.trading_title, ssp.shop_area, tpt.mbid, tpt.transaction_num";}
    else{
        $sprodreleaseSql = "SELECT
			sbid.bidding_id, sbid.shop_id, sbid.shop_name, sbid.trading_title, ssp.shop_area, GROUP_CONCAT(ssi.net_price) 'net_price', '竞价' AS transaction_type,
			GROUP_CONCAT(ssi.title) 'title', GROUP_CONCAT(ssi.num) 'num', GROUP_CONCAT(ssi.unit) 'unit', GROUP_CONCAT(sit.image_default_id) 'img_id',
			tpt.mbid, ifnull(tpt.transaction_num,0) AS transaction_num
		FROM
			sysshoppubt_biddings sbid
		LEFT JOIN sysshop_shop ssp ON sbid.shop_id = ssp.shop_id
		LEFT JOIN sysshoppubt_standard_item ssi ON sbid.uniqid = ssi.uniqid
		LEFT JOIN sysitem_item sit ON ssi.item_id = sit.item_id
		LEFT JOIN (SELECT
			a.bidding_id, b.mbid, c.transaction_num
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
				bidding_id, COUNT(bidding_id) AS transaction_num
			FROM
				sysshoppubt_tradeorder
			GROUP BY
				bidding_id
		) c ON a.bidding_id = c.bidding_id
		AND a.totalbid = b.mbid) tpt ON tpt.bidding_id = sbid.bidding_id
		WHERE
			sbid.is_through = 1 AND sbid.bidding_id IN (-1)
		GROUP BY 
    sbid.bidding_id, sbid.shop_id, sbid.shop_name, sbid.trading_title, ssp.shop_area, tpt.mbid, tpt.transaction_num";
    }
    $sprodreleaseList = app::get("base")->database()->executeQuery($sprodreleaseSql)->fetchAll();
	foreach ($sprodreleaseList as $key => $value) {
		$img=$value["img_id"];
		$imgarr=explode(",", $img);
		$sprodreleaseList[$key]["img_id"]=$imgarr[0];
		$unit=$value["unit"];
		$unitarr=explode(",", $unit);
		$sprodreleaseList[$key]["unit"]=$unitarr[0];
	}
    $setting['sprodreleaseList'] = $sprodreleaseList;

    $tenderids = "";
    foreach ($setting['tenderlist'] as $key => $value) {
    	$tenderids.=",".$value;
    }
    $tenderids = substr($tenderids,1);
    if($tenderids){
    }else{
    	$tenderids = -1;
    }
    $sprodreleaseSql2 = "SELECT
			std.tender_id, std.shop_id, ssp.shop_name, std.trading_title, ssp.shop_area, '' AS net_price, '' AS title,
			GROUP_CONCAT(ssi.num) 'num', GROUP_CONCAT(ssi.unit) 'unit', '' AS mbid,
			GROUP_CONCAT(sit.image_default_id) 'img_id', ifnull(a.transaction_num, 0) AS transaction_num, '招标' AS transaction_type
		FROM
			sysshoppubt_tender std
		LEFT JOIN sysshop_shop ssp ON std.shop_id = ssp.shop_id
		LEFT JOIN sysitem_item sit ON std.shop_id = sit.shop_id
		LEFT JOIN sysshoppubt_standard_item ssi ON std.uniqid = ssi.uniqid
		LEFT JOIN
				(SELECT 
					tender_id, COUNT(tender_id) 'transaction_num' FROM sysshoppubt_tenderenter 
					GROUP BY tender_id) a ON std.tender_id = a.tender_id
		WHERE
			is_through = 1 AND std.tender_id IN (".$tenderids.")
		GROUP BY
			std.tender_id, std.shop_id, std.shop_name, std.trading_title, ssp.shop_area, a.transaction_num";
    $sprodreleaseList2 = app::get("base")->database()->executeQuery($sprodreleaseSql2)->fetchAll();
    	foreach ($sprodreleaseList2 as $key => $value) {
		$img=$value["img_id"];
		$imgarr=explode(",", $img);
		$sprodreleaseList2[$key]["img_id"]=$imgarr[0];
	}
    $setting['sprodreleaseList2'] = $sprodreleaseList2;    	



    return $setting;
}

?>
