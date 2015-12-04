<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_shoplist(&$setting){
    foreach($setting['ncb_select'] as $key=>$value){
        $shop_id =$value;
        $shopList = app::get("sysshop")->model("shop")->getRow("*",array('shop_id'=>$shop_id));
        $_return['ncb_select'][$key]["shop_logo"]=$shopList['shop_logo'];
        $_return['ncb_select'][$key]["shop_id"]=$shopList['shop_id'];
        $_return['ncb_select'][$key]["shop_name"]=$shopList['shop_name'];
    }
        
    return $_return;
}
?>
