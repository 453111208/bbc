<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

// 各种促销关联表
return  array(
    'columns' => array(
        'promotion_id' => array(
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'width' => 110,
            'label' => app::get('syspromotion')->_('促销id'),
            'comment' => app::get('syspromotion')->_('促销id'),
        ),
        'rel_promotion_id' => array(
            'type' => 'number',
            'required' => true,
            'width' => 110,
            'label' => app::get('syspromotion')->_('关联的促销类型id'),
            'comment' => app::get('syspromotion')->_('关联的促销类型id'),
        ),
        'promotion_type' => array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'width' => 110,
            'label' => app::get('syspromotion')->_('促销类型'),
            'comment' => app::get('syspromotion')->_('促销类型'),
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
        'promotion_name' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'width' => 110,
            'label' => app::get('syspromotion')->_('促销名称'),
            'comment' => app::get('syspromotion')->_('促销名称'),
        ),
        'promotion_tag' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'width' => 110,
            'label' => app::get('syspromotion')->_('促销标签'),
            'comment' => app::get('syspromotion')->_('促销标签'),
        ),
        'promotion_desc' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'in_list' => true,
            'width' => 110,
            'label' => app::get('syspromotion')->_('促销描述'),
            'comment' => app::get('syspromotion')->_('促销描述'),
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
        'start_time' => array(
            'type' => 'time',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '100',
            'label' => app::get('syspromotion')->_('开始时间'),
            'comment' => app::get('syspromotion')->_('开始时间'),
        ),
        'end_time' => array(
            'type' => 'time',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'width' => '100',
            'label' => app::get('syspromotion')->_('结束时间'),
            'comment' => app::get('syspromotion')->_('结束时间'),
        ),
        'created_time' => array(
            'type' => 'time',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'width' => '100',
            'label' => app::get('syspromotion')->_('创建时间'),
            'comment' => app::get('syspromotion')->_('创建时间'),
        ),
        'check_status' => array(
            'type' => array(
                'pending' => app::get('syspromotion')->_('待审核'),
                'agree' => app::get('syspromotion')->_('审核通过'),
                'refuse' => app::get('syspromotion')->_('审核拒绝'),
                'cancel' => app::get('syspromotion')->_('已取消'),
            ),
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'width' => 110,
            'label' => app::get('syspromotion')->_('促销状态'),
            'comment' => app::get('syspromotion')->_('促销状态'),
        ),
    ),
    
    'primary' => 'promotion_id',
    'comment' => app::get('syspromotion')->_('各种促销关联表'),
);
