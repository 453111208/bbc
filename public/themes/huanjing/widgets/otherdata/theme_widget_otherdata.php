<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_otherdata(&$setting){
    foreach($setting['data_select'] as $key=>$value)
    {
        if($key<8){
            $data_id =$value;
            $dataList = app::get("sysinfo")->model("otherData")->getList("*",array('data_id'=>$data_id));
            $_return['data_select'][$key]["data"]=$dataList[0];
            $num=explode("%",$dataList[0]['price_run']);
            $_return['data_select'][$key]["nums"]=$num[0];
        }
    }
    return $_return;
}
?>
