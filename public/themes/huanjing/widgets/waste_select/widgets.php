<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


$setting['auther']='dulinchong@irongwei.com';
$setting['version']='v1.0';
$setting['name']='详情页废料分类';
$setting['stime']='2015-10';
$setting['catalog']='行情中心';
$setting['usual'] = '2';
$setting['description'] = '详情页废料分类';
$setting['userinfo'] ='';
$setting['template'] = array(
                            'default.html'=>app::get('b2c')->_('默认')
                        );
$setting['limit']= '20';
$firstSql="select first_sort FROM sysinfo_marketdata a group by first_sort desc";
$sql="SELECT first_sort, second_sort FROM sysinfo_marketdata as a  group by first_sort,second_sort  order by a.date desc ";
$firstList=app::get("base")->database()->executeQuery($firstSql)->fetchAll();
$selectList = app::get("base")->database()->executeQuery($sql)->fetchAll();
$setting["first"]=$firstList;
$setting['selectList']=$selectList;

?>
