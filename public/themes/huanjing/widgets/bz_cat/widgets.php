<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/*基础配置项*/
$setting['author']='tylerchao.sh@gmail.com';
$setting['version']='v1.0';
$setting['name']='环保超市类型';
$setting['order']=0;
$setting['stime']='2013-07';
$setting['catalog']='环保超市';
$setting['description'] = '环保超市类型';
$setting['userinfo'] = '';
$setting['usual']    = '1';
$setting['tag']    = 'auto';
$setting['template'] = array(
                            'default.html'=>app::get('b2c')->_('默认')
                        );

$setting['limit']    = '3';
$catList=app::get("syscategory")->model("cat")->getList("*",array("is_bz"=>1,"level"=>1));
$setting["catlist"]=$catList;
?>
