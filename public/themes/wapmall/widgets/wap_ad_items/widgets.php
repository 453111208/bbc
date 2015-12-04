<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/*基础配置项*/
$setting['author']='gongjiapeng@shopex.cn';
$setting['version']='v1.0';
$setting['name']='首页商品推广';
$setting['order']=0;
$setting['stime']='2015-07';
$setting['catalog']='商品相关';
$setting['description'] = '展示指定商品';
$setting['userinfo'] = '';
$setting['usual']    = '1';
$setting['tag']    = 'auto';
$setting['template'] = array(
                            'default.html'=>app::get('topm')->_('默认')
                        );

$setting['limit']    = '60';
?>