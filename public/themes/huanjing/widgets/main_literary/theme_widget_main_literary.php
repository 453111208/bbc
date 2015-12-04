<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_main_literary(&$setting){
    $literarycatid=$setting["cat"];
     if($literarycatid)
     {
        $literarysql = "select sy.*,st.name,st.nickname from sysexpert_literary sy join sysexpert_expert st on sy.expert_id=st.expert_id  where sy.literarycat_id=".$literarycatid." order by sy.modified desc";
        $literaryInfo = app::get("base")->database()->executeQuery($literarysql)->fetchAll();
        // $literaryInfo=app::get("sysexpert")->model("literary")->getList("*",array("literarycat_id"=>$literarycatid,"towhere"=>1));
        $literarycatInfo=app::get("sysexpert")->model("literarycat")->getRow("*",array("literarycat_id"=>$literarycatid));
    }
    $_return["literaryInfo"]=array_slice($literaryInfo,0,4);
    $_return["literarycatInfo"]=$literarycatInfo;
    return $_return;
}
?>
