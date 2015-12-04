<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_market_article(&$setting){
    foreach($setting['info_select'] as $key=>$value)
    {
        if($key<7){
            $article_id =$value;
            $artList = app::get("sysinfo")->model("marketArticle")->getList("*",array('article_id'=>$article_id));
            $_return['info_select'][$key]["article_id"]=$value;
            $_return['info_select'][$key]["title"]=$artList[0]['title'];
        }
    }
    return $_return;
}
?>
