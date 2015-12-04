<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_main_info(&$setting){
    foreach($setting['node_select'] as $key=>$value)
    {
        if($key<6){
            $node_id =$value;
            $nodeList = app::get("sysinfo")->model("article_nodes")->getList("*",array('node_id'=>$node_id));
            $sql="SELECT * FROM sysinfo_article where node_id='".$node_id."' and status=1 and towhere=1 order by click_rate desc"; 
            $articleList=app::get("base")->database()->executeQuery($sql)->fetchAll();
            $_return['article'][$key]["list"] =  $articleList;
            foreach($articleList as $key1=>$aricle){
                $_return['article'][$key]["articleInfo"][$key1]["article_id"]=$aricle["article_id"];
                $_return['article'][$key]["articleInfo"][$key1]["title"]=$aricle["title"];
                $_return['article'][$key]["articleInfo"][$key1]["subhead"]=$aricle["subhead"];
                $_return['article'][$key]["articleInfo"][$key1]["article_logo"]=$aricle["article_logo"];
                $_return['article'][$key]["articleInfo"][$key1]["click_rate"]=$aricle["click_rate"];
            }
            $_return['node_select'][$key]["node"]=$nodeList[0]['node_name'];
            $_return['node_select'][$key]["node_id"]=$node_id;
        }
    }
    return $_return;
}
?>
