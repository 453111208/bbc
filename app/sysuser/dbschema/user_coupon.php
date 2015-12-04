<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
/**
* @table member_coupon;
*
* @package Schemas
* @version $
* @copyright 2010 ShopEx
* @license Commercial
*/

return  array(
    'columns' => array(
        'coupon_code' => array(
            //'type' => 'varchar(32)',
            'type' => 'string',
            'length' => 32,
            'required' => true,
            //'pkey' => true,
            'label' => app::get('sysuser')->_('优惠券号码'),
            'comment' => app::get('sysuser')->_('优惠券号码'),
        ),
        'user_id' => array(
            'type' => 'number',
            'required' => true,
            //'pkey'=>true,
            'label' => app::get('sysuser')->_('会员ID'),
            'comment' => app::get('sysuser')->_('会员ID'),
        ),
        'shop_id' => array(
            'type' => 'number',
            'required' => true,
            'default' => 0,
            //'pkey'=>true,
            'label' => app::get('sysuser')->_('店铺ID'),
            'comment' => app::get('sysuser')->_('店铺ID'),
        ),
        'coupon_id' => array(
            'type' => 'number',
            'required' => true,
            'default' => 0,
            'label' => app::get('sysuser')->_('会员优惠券ID'),
            'comment' => app::get('sysuser')->_('会员优惠券ID'),
        ),
        'obtain_desc' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'width' => 110,
            'label' => app::get('sysuser')->_('领取方式'),
            'comment' => app::get('sysuser')->_('领取方式'),
        ),
        'obtain_time' => array(
            'type' => 'time',
            'label' => app::get('sysuser')->_('优惠券获得时间'),
            'comment' => app::get('sysuser')->_('优惠券获得时间'),
        ),
        'tid' => array(
            //'type' => 'bigint unsigned',
            'type' => 'bigint',
            'unsigned' => true,
            'label' => app::get('sysuser')->_('使用该优惠券的订单号'),
            'comment' => app::get('sysuser')->_('使用该优惠券的订单号'),
        ),
        'is_valid' => array(
            'type' => array(
                '0' => app::get('sysuser')->_('已使用'),
                '1' => app::get('sysuser')->_('有效'),
                '2' => app::get('sysuser')->_('过期'),
            ),
            'default' => 1,
            'required' => true,
            'editable' => false,
            'label' => app::get('sysuser')->_('会员优惠券是否当前可用'),
            'comment' => app::get('sysuser')->_('会员优惠券是否当前可用'),
        ),
        'used_platform' => array(
            'type' => array(
                '0' => app::get('sysuser')->_('商家全场可用'),
                '1' => app::get('sysuser')->_('只能用于pc'),
                '2' => app::get('sysuser')->_('只能用于wap'),
            ),
            'default' => 0,
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysuser')->_('使用平台'),
            'comment' => app::get('sysuser')->_('使用平台'),
        ),
    ),
    
    'primary' => ['coupon_code', 'user_id', 'shop_id'],
    'comment' => app::get('sysuser')->_('用户优惠券表'),
);
