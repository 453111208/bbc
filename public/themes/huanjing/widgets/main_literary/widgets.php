<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


$setting['auther']='dulinchong@irongwei.com';
$setting['version']='v1.0';
$setting['name']='名人专家首页挂件';
$setting['stime']='2015-10';
$setting['catalog']='名人专家';
$setting['usual'] = '2';
$setting['description'] = '名人专家首页挂件';
$setting['userinfo'] ='';
$setting['template'] = array(
                            'default.html'=>app::get('b2c')->_('默认')
                        );

$setting['limit']    = '4';
$literarycatInfo=app::get("sysexpert")->model("literarycat")->getList("*");
$setting["literarycatList"]=$literarycatInfo;


?>
