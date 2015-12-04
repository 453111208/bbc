<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


$setting['author']='tylerchao.sh@gmail.com';
$setting['name'] ='交易分类';
$setting['version'] ='v1.0';
$setting['stime'] ='2015-10';
$setting['catalog'] ='广告相关';
$setting['usual'] = '1';
$setting['tag']='auto';
$setting['description'] = '按商品类别分类交易.';
$setting['userinfo'] = '按照商品的一级分类来区分交易.';
$setting['template'] = array(
                            'default.html'=>app::get('b2c')->_('默认')
                        );
 $userMdlcat = app::get("sysspfb")->model('cat');
 $catList =  $userMdlcat ->getList("*",array('level'=>"1"));
 $setting['catList'] = $catList;
?>
