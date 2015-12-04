<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_expert_focus(&$setting)
{
    foreach($setting['articles_focus'] as $key=>$value)
    {
        if($key<3){
            $literary_id =$value;
            $literaryList = app::get("sysexpert")->model("literary")->getList("*",array('literary_id'=>$literary_id));
            $_return['articles_focus'][$key]['title']=mb_substr($literaryList[0]['title'], 0,16,'utf-8');
            $_return['articles_focus'][$key]['literaryId']=$literary_id;
        }
    }
    
    return $_return;
}

?>
