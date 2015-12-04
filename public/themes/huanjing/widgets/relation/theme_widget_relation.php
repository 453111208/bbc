<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_relation(&$setting){
    foreach($setting['relation'] as $key=>$value)
    {
        if($key<2){
        $essay_id =$value;
        $essaycatid = app::get("syscase")->model("essay")->getRow("*",array('essay_id'=>$essay_id));
        $article = app::get("sysinfo")->model("article")->getList("*",array('essaycat_id'=>$essaycatid["essaycat_id"]));
        $_return['relation'][$key]["essay"]=$essaycatid;
        $_return['relation'][$key]["article"]=$article;
        }
    }
    return $_return;
}
?>
