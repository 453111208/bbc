<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_resolve_case2(&$setting){
    foreach($setting['resolve_case2'] as $key=>$value)
    {
        $essay_id =$value;
        $essaylist = app::get("syscase")->model("essay")->getList("*",array('essay_id'=>$essay_id));

        $id=$essaylist[0]["essaycat_id"];
        $nameInfo=app::get("syscase")->model("essaycat")->getRow("essaycat",array('essaycat_id'=>$id));
        $name=$nameInfo['essaycat'];
        $essaylist[0]["essaycat"]=$name;
        
        $_return['resolve_case2'][$key]["data_id"]=$value;
        $_return['resolve_case2'][$key]["data"]=$essaylist[0];
        $_return["secondSort"]=$essaylist[0]['second_sort'];
        $date=explode("年",$essaylist[0]['date']);
        $_return['resolve_case2'][$key]["date"]=$date[1];
        //var_dump($_return['resolve_case2'][$key]);
    }
    return $_return;
}
?>
