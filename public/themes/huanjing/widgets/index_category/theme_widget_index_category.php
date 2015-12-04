<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_index_category(&$setting)
{
    // 判断是否首页
    $A=route::currentRouteName();
    //var_dump($_SERVER);
    if (route::currentRouteName() == 'topc'||$_SERVER["ORIG_PATH_INFO"]=="/index.php/trading"||$_SERVER["DOCUMENT_URI"]=="/index.php/trading")
    {
        $returnData['isindex'] = true;
    }
    if( false&& base_kvstore::instance('topc_category')->fetch('category_ex_vertical_widget.data',$cat_list) ){
        return $cat_list;
    }
    $returnData['data'] = app::get('topc')->rpcCall('category.cat.get.list',array('fields'=>'cat_id,cat_name'));
    //var_dump($returnData);
    return $returnData;
}
?>

