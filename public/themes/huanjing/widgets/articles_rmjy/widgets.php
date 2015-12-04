<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


$setting['author']='tylerchao.sh@gmail.com';
$setting['name'] ='热门交易';
$setting['version'] ='v1.0';
$setting['stime'] ='2013-07';
$setting['catalog'] ='资讯中心';
$setting['usual'] = '1';
$setting['tag']='auto';
$setting['description'] = '热门交易.';
$setting['userinfo'] = '热门交易.';
$setting['template'] = array(
                            'default.html'=>app::get('b2c')->_('默认')
                        );

$now=time();
$biddingSql = "SELECT bidding_id, trading_title FROM sysshoppubt_biddings WHERE is_through = 1 AND isok<>1 AND start_time<".$now." AND stop_time >".$now."  order by create_time ";
$biddingList = app::get("base")->database()->executeQuery($biddingSql)->fetchAll();
$setting['biddingList'] = $biddingList;

$tenderSql = "SELECT tender_id, trading_title FROM sysshoppubt_tender WHERE is_through = 1 AND isok<>1 AND start_time<".$now." AND stop_time >".$now."  order by create_time ";
$tenderList = app::get("base")->database()->executeQuery($tenderSql)->fetchAll();
$setting['tenderList'] = $tenderList;
?>
