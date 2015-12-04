<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

// function theme_widget_info(&$setting){
    /*foreach($setting['info_select'] as $key=>$value)
    {
        if($key<8){
            $article_id =$value;
            $artList = app::get("sysinfo")->model("article")->getList("*",array('article_id'=>$article_id));
            $node_id=$artList[0]['node_id'];
            $nodeList = app::get("sysinfo")->model("article_nodes")->getList("*",array('node_id'=>$node_id));
            $_return['info_select'][$key]["article_id"]=$value;
            $_return['info_select'][$key]["title"]=$artList[0]['title'];
            $_return['info_select'][$key]["node"]=$nodeList[0]['node_name'];
            $_return['info_select'][$key]["node_id"]=$node_id;
        }
    }*/
function theme_widget_info(){
    $sql="select * from sysinfo_article where towhere =1 and istop =1 order by click_rate desc LIMIT 8 ";
    $artlist = app::get("base")->database()->executeQuery($sql)->fetchAll();
    foreach($artlist as $key=>$value){
    $_return[$key]["title"]=$value['title'];
    $_return[$key]["article_id"]=$value['article_id'];
    }
    // var_dump($_return); 
    return $_return;
}
?>
