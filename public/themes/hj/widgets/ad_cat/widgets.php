<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


$setting['author']='tylerchao.sh@gmail.com';
$setting['name'] ='三级类目推荐';
$setting['version'] ='v1.0';
$setting['stime'] ='2014-10';
$setting['catalog'] ='广告相关';
$setting['usual'] = '1';
$setting['tag']='auto';
$setting['description'] = '支持所有HTML定义.';
$setting['userinfo'] = '在源码编辑中，直接输入html格式的代码.';
$setting['template'] = array(
                            'default.html'=>app::get('b2c')->_('默认')
                        );
$lv1Cat = app::get('topc')->rpcCall('category.cat.get.info',array('parent_id'=>0,'fields'=>'cat_id,cat_name'));
foreach($lv1Cat as $val)
{
    $selectmaps[$val['cat_id']] = $val['cat_name'];
}
$setting['selectmaps'] = $selectmaps;

?>
