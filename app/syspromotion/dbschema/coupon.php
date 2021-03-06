<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

// 优惠券规则表
return  array(
    'columns' => array(
        'coupon_id' => array(
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'width' => 110,
            'label' => app::get('syspromotion')->_('id'),
            'comment' => app::get('syspromotion')->_('优惠券方案id'),
        ),
        'shop_id' => array(
            'type' => 'number',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'width' => 100,
            'label' => app::get('syspromotion')->_('所属商家'),
            'comment' => app::get('syspromotion')->_('所属商家的店铺id'),
        ),
        'coupon_name' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'width' => 110,
            'label' => app::get('syspromotion')->_('优惠券名称'),
            'comment' => app::get('syspromotion')->_('优惠券名称'),
        ),
        'coupon_desc' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'required' => true,
            'in_list' => true,
            'width' => 110,
            'label' => app::get('syspromotion')->_('优惠券描述'),
            'comment' => app::get('syspromotion')->_('优惠券描述'),
        ),
        'used_platform' => array(
            'type' => array(
                '0' => app::get('syspromotion')->_('商家全场可用'),
                '1' => app::get('syspromotion')->_('只能用于pc'),
                '2' => app::get('syspromotion')->_('只能用于wap'),
            ),
            'default' => 0,
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('syspromotion')->_('使用平台'),
            'comment' => app::get('syspromotion')->_('使用平台'),
        ),
        'valid_grade' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'default' => '',
            'required' => true,
            'label' => app::get('syspromotion')->_('会员级别集合'),
        ),
        'limit_money' => array(
            'type' => 'money',
            'default' => '0',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '50',
            'order' => 14,
            'label' => app::get('syspromotion')->_('满足条件金额'),
            'comment' => app::get('syspromotion')->_('满足条件金额'),
        ),
        'deduct_money' => array(
            'type' => 'money',
            'default' => '0',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '50',
            'order' => 14,
            'label' => app::get('syspromotion')->_('优惠金额'),
            'comment' => app::get('syspromotion')->_('优惠金额'),
        ),
        'max_gen_quantity' => array(
            'type' => 'number',
            'default' => '0',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '50',
            'order' => 14,
            'label' => app::get('syspromotion')->_('最大优惠券号码数量'),
            'comment' => app::get('syspromotion')->_('最大优惠券号码数量'),
        ),
        'send_couponcode_quantity' => array(
            'type' => 'number',
            'default' => '0',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '50',
            'order' => 14,
            'label' => app::get('syspromotion')->_('已生成的优惠券号码数量'),
            'comment' => app::get('syspromotion')->_('已生成的优惠券号码数量'),
        ),
        'userlimit_quantity' => array(
            'type' => 'number',
            'default' => '0',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '50',
            'order' => 14,
            'label' => app::get('syspromotion')->_('用户总计可领取优惠券数量'),
            'comment' => app::get('syspromotion')->_('用户总计可领取优惠券数量'),
        ),
        'use_bound' => array(
            'type' => array(
                '0' => app::get('syspromotion')->_('商家全场可用'),
                '1' => app::get('syspromotion')->_('指定商品可用'),
            ),
            'default' => '1',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '50',
            'order' => 14,
            'label' => app::get('syspromotion')->_('使用范围'),
            'comment' => app::get('syspromotion')->_('使用范围'),
        ),
        'coupon_prefix' => array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'required' => true,
            'default' => '',
            'in_list' => true,
            'default_in_list' => true,
            'width' => 110,
            'label' => app::get('syspromotion')->_('优惠券前缀'),
            'comment' => app::get('syspromotion')->_('优惠券前缀'),
        ),
        'coupon_key' => array(
            //'type' => 'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'required' => true,
            'default' => '',
            'width' => 110,
            'label' => app::get('syspromotion')->_('优惠券生成的key'),
            'comment' => app::get('syspromotion')->_('优惠券生成的key'),
        ),
        'cansend_start_time' => array(
            'type' => 'time',
            'width' => '100',
            'label' => app::get('syspromotion')->_('发优惠券开始时间'),
            'comment' => app::get('syspromotion')->_('发优惠券开始时间'),
        ),
        'cansend_end_time' => array(
            'type' => 'time',
            'width' => '100',
            'label' => app::get('syspromotion')->_('发优惠券结束时间'),
            'comment' => app::get('syspromotion')->_('发优惠券结束时间'),
        ),
        'canuse_start_time' => array(
            'type' => 'time',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '100',
            'label' => app::get('syspromotion')->_('优惠券生效时间'),
            'comment' => app::get('syspromotion')->_('优惠券生效时间'),
        ),
        'canuse_end_time' => array(
            'type' => 'time',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '100',
            'label' => app::get('syspromotion')->_('优惠券失效时间'),
            'comment' => app::get('syspromotion')->_('优惠券失效时间'),
        ),
        'created_time' => array(
            'type' => 'time',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '100',
            'label' => app::get('syspromotion')->_('建券时间'),
            'comment' => app::get('syspromotion')->_('建券时间'),
        ),
        'promotion_tag' => array(
            //'type' => 'varchar(15)',
            'type' => 'string',
            'length' => 15,
            'required' => true,
            'label' => app::get('syspromotion')->_('促销标签'),
            'comment' => app::get('syspromotion')->_('促销标签'),
        ),
        'coupon_status' => array(
            'type' => array(
                'pending' => app::get('syspromotion')->_('待审核'),
                'agree' => app::get('syspromotion')->_('审核通过'),
                'refuse' => app::get('syspromotion')->_('审核拒绝'),
                'cancel' => app::get('syspromotion')->_('已取消'),
            ),
            'default' => 'agree',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'width' => 110,
            'label' => app::get('syspromotion')->_('促销状态'),
            'comment' => app::get('syspromotion')->_('促销状态'),
        ),
    ),

    'primary' => 'coupon_id',
    'comment' => app::get('syspromotion')->_('优惠券表'),
);
