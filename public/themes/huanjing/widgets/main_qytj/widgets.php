<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


$setting['author']='tylerchao.sh@gmail.com';
$setting['name'] ='首页产废回收企业推荐';
$setting['version'] ='v1.0';
$setting['stime'] ='2013-07';
$setting['catalog'] ='企业库';
$setting['usual'] = '1';
$setting['tag']='auto';
$setting['description'] = '首页产废回收企业推荐.';
$setting['userinfo'] = '首页产废回收企业推荐.';
$setting['template'] = array(
                            'default.html'=>app::get('b2c')->_('默认')
                        );

$srSql = "SELECT ssp.*, ssr.seller_type
		FROM sysshop_shop ssp
		LEFT JOIN sysshop_seller ssr ON ssp.seller_id = ssr.seller_id
		WHERE ssr.seller_type in (1,2)";
$srList = app::get("base")->database()->executeQuery($srSql)->fetchAll();
$hsshop = array();
$cfshop = array();
for($i=0; $i<count($srList,0); $i++) {
	$seller_type = $srList[$i]["seller_type"];
	if($seller_type == 1){
		array_push($hsshop, $srList[$i]);
	} else if ($seller_type == 2) {
		array_push($cfshop, $srList[$i]);
	}
}
$setting['hsshop'] = $hsshop;
$setting['cfshop'] = $cfshop;
?>
