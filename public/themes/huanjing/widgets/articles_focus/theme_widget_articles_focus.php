<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_articles_focus(&$setting)
{
    foreach($setting['articles_focus'] as $key=>$value)
    {
        if($key<6){
            $literary_id =$value;
            $literaryList = app::get("sysexpert")->model("literary")->getList("*",array('literary_id'=>$literary_id));
            $_return['articles_focus'][$key]['title']=$literaryList[0]['title'];
            $_return['articles_focus'][$key]['literaryId']=$literary_id;
        }
    }
    
    return $_return;
}

?>
