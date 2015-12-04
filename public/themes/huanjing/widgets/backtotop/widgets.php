<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


$setting['author']='tylerchao.sh@gmail.com';
$setting['name']='返回顶部';
$setting['version']='v1.0';
$setting['stime']='2014-10';
$setting['catalog']='辅助信息';
$setting['description']    = '从页面的非顶部返回顶部';
$setting['usual']    = '0';
$setting['template'] = array(
                            'default.html'=>app::get('topc')->_('侧边栏返回顶部')
                        );
?>
