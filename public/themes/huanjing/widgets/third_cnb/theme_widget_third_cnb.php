<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_third_cnb(&$setting){
    foreach($setting['ncb_select'] as $key=>$value){
        if($key<12){
            $shop_id =$value;
            $shopList = app::get("sysshop")->model("shop")->getRow("*",array('shop_id'=>$shop_id));
            $_return['ncb_select'][$key]["shop_logo"]=$shopList['shop_logo'];
            $_return['ncb_select'][$key]["shop_id"]=$shopList['shop_id'];
        }
    }
        
    return $_return;
}
?>
