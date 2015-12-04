<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_third_service(&$setting){
    foreach($setting['service_select'] as $key=>$value)
	{
        $article_id =$value;
        $artList = app::get("syscontent")->model("article")->getList("*",array('article_id'=>$article_id));
        $_return['service_select'][$key]["content"]=$artList[0]['content'];
        $_return['service_select'][$key]["title"]=$artList[0]['title'];
        //var_dump($_return['article_select'][$key]);
    }
        
    return $_return;
}
?>
