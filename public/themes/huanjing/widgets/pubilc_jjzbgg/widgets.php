<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


$setting['author']='tylerchao.sh@gmail.com';
$setting['name'] ='竞价招标公告';
$setting['version'] ='v1.0';
$setting['stime'] ='2013-07';
$setting['catalog'] ='广告相关';
$setting['usual'] = '1';
$setting['tag']='auto';
$setting['description'] = '竞价招标公告.';
$setting['userinfo'] = '竞价招标公告.';
$setting['template'] = array(
                            'default.html'=>app::get('b2c')->_('默认')
                        );

$sprodreleaseSql = "SELECT t1.* FROM (SELECT
			concat(sbid.bidding_id,';1') 'bidorten_id', sbid.shop_id, sbid.shop_name, sbid.trading_title, ssp.shop_area, ssi.net_price,
			GROUP_CONCAT(ssi.title) 'title', GROUP_CONCAT(ssi.num) 'num', GROUP_CONCAT(ssi.unit) 'unit', GROUP_CONCAT(sit.image_default_id) 'img_id', 
			tpt.mbid, tpt.cbid, sbid.stop_time, '1' AS bidorten_type, '竞价' AS bidorten_type_name 
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
			sbid.is_through = 1 AND sbid.isok = 1
		GROUP BY 
		  sbid.bidding_id, sbid.shop_id, sbid.shop_name, sbid.trading_title, ssp.shop_area, ssi.net_price, tpt.mbid, tpt.cbid

		UNION

		SELECT
			concat(std.tender_id,';2') 'bidorten_id', std.shop_id, ssp.shop_name, std.trading_title, ssp.shop_area, '' AS net_price, '' AS title, '' AS num, '' AS unit, 
			GROUP_CONCAT(sit.image_default_id) 'img_id', '' AS mbid, '' AS cbid, std.stop_time, '2' AS bidorten_type, '招标' AS bidorten_type_name  
		FROM
			sysshoppubt_tender std
		LEFT JOIN sysshop_shop ssp ON std.shop_id = ssp.shop_id
		LEFT JOIN sysitem_item sit ON std.shop_id = sit.shop_id
		WHERE
			is_through = 1 AND std.isok = 1
		GROUP BY
			std.tender_id, std.shop_id, std.shop_name, std.trading_title, ssp.shop_area) t1 ORDER BY t1.stop_time DESC";
$sprodreleaseList = app::get("base")->database()->executeQuery($sprodreleaseSql)->fetchAll();
$setting['jjzbList'] = $sprodreleaseList;
?>
