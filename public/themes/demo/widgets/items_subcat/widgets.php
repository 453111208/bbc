<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


$setting['author']='tylerchao.sh@gmail.com';
$setting['version']='v1.0';
$setting['name']='二、三级类目挂件(一级类目专题页面)';
$setting['catalog']='商品相关';
$setting['usual'] = '0';
$setting['description']= '展示模板使用的商品搜索挂件';
$setting['stime']='2013-07';
$setting['userinfo']='';
$setting['template'] = array(
                            'default.html'=>app::get('b2c')->_('默认'),
                        );
#$setting['vary'] = microtime();
$lv1Cat = app::get('topc')->rpcCall('category.cat.get.info',array('parent_id'=>0,'fields'=>'cat_id,cat_name'));
foreach($lv1Cat as $val)
{
    $selectmaps[$val['cat_id']] = $val['cat_name'];
}
$setting['selectmaps'] = $selectmaps;
$setting['lv1_cat_id']  = 1;       //默认类目id
?>
