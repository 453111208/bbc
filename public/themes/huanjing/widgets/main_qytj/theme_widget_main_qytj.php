<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_main_qytj(&$setting)
{
    $hsshop = array();
    $cfshop = array();
    foreach ($setting['hslist'] as $key => $value) {
    	$shop_id = $value;
    	$artList = app::get("sysshop")->model("shop")->getList("*",array('shop_id'=>$shop_id));
    	array_push($hsshop, $artList[0]);
    }
    foreach ($setting['cflist'] as $key => $value) {
    	$shop_id = $value;
    	$artList = app::get("sysshop")->model("shop")->getList("*",array('shop_id'=>$shop_id));
    	array_push($cfshop, $artList[0]);
    }
    $setting['hsshop'] = $hsshop;
    $setting['cfshop'] = $cfshop;
    return $setting;
}

?>
