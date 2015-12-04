<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_tpos(&$setting){
    foreach($setting['article_select'] as $key=>$value)
	{
        $article_id =$value;
        $artList = app::get("syscontent")->model("article")->getList("*",array('article_id'=>$article_id));
        $_return['article_select'][$key]["pic1"]=$artList[0]['article_logo'];
        $_return['article_select'][$key]["a11"]=$artList[0]['content'];
        $_return['article_select'][$key]["title"]=$artList[0]['title'];
        //var_dump($_return['article_select'][$key]);
    }
        
    return $_return;
}
?>
