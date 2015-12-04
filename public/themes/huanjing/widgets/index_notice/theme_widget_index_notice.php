<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_index_notice(&$setting){
    foreach($setting['info_select'] as $key=>$value)
    {
        if($key<3){
            $notice_id =$value;
            $noticeList = app::get("sysnotice")->model("notice_item")->getList("*",array('notice_id'=>$notice_id));
            $_return['info_select'][$key]["content"]=$noticeList[0]['notice_content'];
            $_return['info_select'][$key]["title"]=mb_substr($noticeList[0]['notice_name'], 0,16,'utf-8');
        }
    }
    return $_return;
}
?>
