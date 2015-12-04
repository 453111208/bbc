<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

$currency = kernel::single('ectools_data_currency')->getCurrency('all');
$setting = array(
'site.decimal_digit.count'=>array('type'=>SET_T_ENUM,'default'=>2,'desc'=>app::get('ectools')->_('金额运算精度保留位数'),'options'=>array(0=>app::get('ectools')->_('整数取整'),1=>app::get('ectools')->_('取整到1位小数'),2=>app::get('ectools')->_('取整到2位小数'),3=>app::get('ectools')->_('取整到3位小数'))),//WZP
'site.decimal_type.count'=>array('type'=>SET_T_ENUM,'default'=>1,'desc'=>app::get('ectools')->_('金额运算精度取整方式'),'options'=>array('1'=>app::get('ectools')->_('四舍五入'),'2'=>app::get('ectools')->_('向上取整'),'3'=>app::get('ectools')->_('向下取整'))),//WZP
'system.money.decimals'=>array('type'=>SET_T_ENUM,'default'=>2,'desc'=>app::get('ectools')->_('金额显示精度保留位数'),'options'=>array(0=>app::get('ectools')->_('无小数位'),1=>app::get('ectools')->_('1位小数'),2=>app::get('ectools')->_('2位小数'),3=>app::get('ectools')->_('3位小数'))),
'system.money.operation.carryset'=>array('type'=>SET_T_ENUM,'default'=>0,'desc'=>app::get('ectools')->_('金额显示精度取整方式'),'options'=>array(0=>app::get('ectools')->_('四舍五入'),1=>app::get('ectools')->_('向上取整'),2=>app::get('ectools')->_('向下取整'))),
'system.currency.default'=>array('type'=>SET_T_ENUM,'default'=>'CNY','desc'=>app::get('ectools')->_('商城交易货币符号'),'options'=>$currency),//没有税率
'site.decimal_digit.display'=>array('type'=>SET_T_ENUM,'default'=>2,'desc'=>app::get('ectools')->_('金额显示保留位数'),'options'=>array(0=>app::get('ectools')->_('整数取整'),1=>app::get('ectools')->_('取整到1位小数'),2=>app::get('ectools')->_('取整到2位小数'),3=>app::get('ectools')->_('取整到3位小数'))),//WZP
'site.decimal_type.display'=>array('type'=>SET_T_ENUM,'default'=>1,'desc'=>app::get('ectools')->_('金额显示取整方式'),'options'=>array('1'=>app::get('ectools')->_('四舍五入'),'2'=>app::get('ectools')->_('向上取整'),'3'=>app::get('ectools')->_('向下取整'))),
'site.paycenter.pay_succ'=>array('type'=>SET_T_TXT,'default'=>'<a href="'.kernel::base_url(1).'/index.php" type="url" title="返回首页">返回首页</a><br/>（此为默认内容，具体内容可以在后台“页面管理-提示信息管理”中修改）','desc'=>app::get('ectools')->_('支付成功提示自定义信息')),
'site.paycenter.pay_failure'=>array('type'=>SET_T_TXT,'default'=>'<a href="'.kernel::base_url(1).'/index.php" type="url" title="返回首页">返回首页</a><br/>
（此为默认内容，具体内容可以在后台“页面管理-提示信息管理”中修改）','desc'=>app::get('ectools')->_('支付失败提示自定义信息')),
);
