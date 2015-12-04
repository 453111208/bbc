<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2013 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

/*基础配置项*/
$setting['author']='zinkwind@gmail.com';
$setting['version']='v1.0';
$setting['name']='首页主推品类展示';
$setting['order']=0;
$setting['stime']='2015-03';
$setting['catalog']='主推品类';
$setting['description'] = '展示模板使用的首页主推品类展示挂件';
$setting['userinfo'] = '';
$setting['usual']    = '1';
$setting['tag']    = 'auto';
$setting['template'] = array(
                            'default.html'=>app::get('topm')->_('默认')
                        );

/*初始化配置项*/
/*$setting['selector'] = 'filter';
$setting['limit'] = 2;*/
?>
