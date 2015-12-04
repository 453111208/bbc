<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_expert_policy(&$setting){
    $literarycat=$setting['sort'];
    $sqlId="SELECT literarycat_id FROM sysexpert_literarycat where literarycat='".$literarycat."'";
    $Id = app::get("base")->database()->executeQuery($sqlId)->fetchAll();
    $literarycatId=$Id[0]['literarycat_id'];
    $sql="SELECT * FROM sysexpert_literary where literarycat_id='".$literarycatId."' and towhere=1 order by click_count desc limit 4";
    $list = app::get("base")->database()->executeQuery($sql)->fetchAll();
    $_return['list']=$list;
    $_return['literarycatId']=$literarycatId;
    $_return['literarycat']=$literarycat;
    
    return $_return;
    
}
?>


