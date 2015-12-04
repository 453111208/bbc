<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_index_info(&$setting){
    foreach($setting['info_select'] as $key=>$value)
    {
        if($key<3){
            $article_id =$value;
            $artList = app::get("sysinfo")->model("article")->getList("*",array('article_id'=>$article_id));
            $node_id=$artList[0]['node_id'];
            $nodeList = app::get("sysinfo")->model("article_nodes")->getList("*",array('node_id'=>$node_id));
            $_return['info_select'][$key]["article_id"]=$value;
            $_return['info_select'][$key]["title"]=mb_substr($artList[0]['title'], 0,16,'utf-8');
            $_return['info_select'][$key]["node"]=$nodeList[0]['node_name'];
            $_return['info_select'][$key]["node_id"]=$node_id;
        }
    }
    return $_return;
}
?>
