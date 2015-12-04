<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_stores_offer(&$setting){
    foreach($setting['offer_select'] as $key=>$value)
    {
        if($key<5){
            $offer_id =$value;
            $offerList = app::get("sysinfo")->model("offer")->getList("*",array('offer_id'=>$offer_id));
            $_return['offer_select'][$key]=$offerList[0];
        }
    }
    return $_return;
}
?>
