<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_new_offer(&$setting){
    foreach($setting['sort_select'] as $key=>$value)
    {
        if($key<7){
            $data_id =$value;
            $dataList = app::get("sysinfo")->model("marketdata")->getList("*",array('data_id'=>$data_id));
            $_return['sort_select'][$key]["data_id"]=$value;
            $_return['sort_select'][$key]["data"]=$dataList[0];
            $_return["secondSort"]=$dataList[0]['second_sort'];
            $date=explode("年",$dataList[0]['date']);
            $_return['sort_select'][$key]["date"]=$date[1];
        }
    }
    return $_return;
}
?>
