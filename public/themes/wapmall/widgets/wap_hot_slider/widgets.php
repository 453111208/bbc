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
$setting['name']='推荐店铺/商品';
$setting['order']=0;
$setting['stime']='2015-03';
$setting['catalog']='推荐店铺/商品';
$setting['description'] = '推荐店铺/商品';
$setting['userinfo'] = '';
$setting['usual']    = '1';
$setting['tag']    = 'auto';
$setting['template'] = array(
                            'default.html'=>app::get('topm')->_('默认')
                        );
$setting['limit']    = '9';
?>
