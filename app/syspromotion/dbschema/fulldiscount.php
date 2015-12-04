<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */

//商品促销规则表
return array(
    'columns' => array(
        'fulldiscount_id' => array(
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'label' => app::get('syspromotion')->_('满折规则id'),
            'comment' => app::get('syspromotion')->_('满折规则id'),
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
        'fulldiscount_name' => array(
            'type' => 'string',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'filterdefault'=>true,
            'is_title' => true,
            'label' => app::get('syspromotion')->_('满折规则名称'),
            'comment' => app::get('syspromotion')->_('满折规则名称'),
        ),
        'fulldiscount_desc' => array(
            'type' => 'text',
            'default' => '',
            'in_list' => true,
            'label' => app::get('syspromotion')->_('规则描述'),
            'comment' => app::get('syspromotion')->_('规则描述'),
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
        'valid_grade' => array(
            'type' => 'string',
            'default' => '',
            'required' => false,
            'label' => app::get('syspromotion')->_('会员级别集合'),
        ),
        'condition_value' => array(
            'type' => 'string',
            'default' => '',
            'required' => false,
            'label' => app::get('syspromotion')->_('满折值'),
        ),
        'join_limit' => array(
            'type' => 'number',
            'default' => 1,
            'required' => true,
            'label' => app::get('syspromotion')->_('可参与次数'),
        ),
        'free_postage' => array(
            'type' => 'bool',
            'default' => '0',
            'required' => false,
            'label' => app::get('syspromotion')->_('是否免邮'),
        ),
        'created_time' => array(
            'type' => 'time',
            'in_list' => true,
            'default_in_list' => false,
            'filterdefault'=>true,
            'label' => app::get('syspromotion')->_('创建时间'),
            'comment' => app::get('syspromotion')->_('创建时间'),
        ),
        'start_time' => array(
            'type' => 'time',
            'default'=> 0,
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
            'filterdefault'=>true,
            'label' => app::get('syspromotion')->_('起始时间'),
            'comment' => app::get('syspromotion')->_('起始时间'),
        ),
        'end_time' => array(
            'type' => 'time',
            'default'=> 0,
            'editable' => true,
            'in_list' => true,
            'default_in_list' => false,
            'filterdefault'=>true,
            'label' => app::get('syspromotion')->_('截止时间'),
            'comment' => app::get('syspromotion')->_('截止时间'),
        ),
        'promotion_tag' => array(
            'type' => 'string',
            'length' => 15,
            'required' => true,
            'label' => app::get('syspromotion')->_('促销标签'),
            'comment' => app::get('syspromotion')->_('促销标签'),
        ),
        'fulldiscount_status' => array(
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
    
    'primary' => 'fulldiscount_id',
    'comment' => app::get('syspromotion')->_('满折促销规则表'),
);
