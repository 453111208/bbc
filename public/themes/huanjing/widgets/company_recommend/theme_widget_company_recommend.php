<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_company_recommend(&$setting){
    foreach($setting['shop_select'] as $key=>$value)
    {
        if($key<8){
            $shop_id =$value;
            $shopList = app::get("sysshop")->model("shop")->getList("*",array('shop_id'=>$shop_id));
            $_return['shop_select'][$key]=$shopList[0];
        }
    }
    return $_return;
}
?>
