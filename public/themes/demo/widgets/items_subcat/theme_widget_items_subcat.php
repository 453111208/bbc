<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

function theme_widget_items_subcat(&$setting)
{

    if($setting['lv1_cat_id']){
        $filter['cat_id'] = $setting['lv1_cat_id'];
    }
    if($filter['cat_id'])
    {
    $returnData = app::get('topc')->rpcCall('category.cat.get',array('fields'=>'cat_id,cat_name','cat_id'=>$filter['cat_id']));
    }
    else
    {
    $returnData = app::get('topc')->rpcCall('category.cat.get.list',array('fields'=>'cat_id,cat_name'));
    }
    return $returnData;
}
