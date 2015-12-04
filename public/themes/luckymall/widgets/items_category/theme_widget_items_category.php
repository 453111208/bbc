<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_items_category(&$setting)
{
    // 判断是否首页
    if (route::currentRouteName() == 'topc')
    {
        $returnData['isindex'] = true;
    }
    if( false&& base_kvstore::instance('topc_category')->fetch('category_ex_vertical_widget.data',$cat_list) ){
        return $cat_list;
    }
    $returnData['data'] = app::get('topc')->rpcCall('category.cat.get.list',array('fields'=>'cat_id,cat_name,cat_logo'));
    //echo '<pre>';print_r($returnData['data']);exit();
    return $returnData;
}
?>

