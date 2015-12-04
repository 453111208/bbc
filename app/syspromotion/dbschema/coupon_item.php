<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */

//商品与促销规则关联表
return  array(
    'columns' => array(
        'coupon_id' => array(
            'type' => 'number',
            'required' => true,
            'comment' => app::get('syspromotion')->_('优惠券ID'),
        ),
        'item_id' => array(
            'type' => 'number',
            'required' => true,
            'comment' => app::get('syspromotion')->_('商品ID'),
        ),
        'leaf_cat_id' => array(
            'type' => 'number',
            'required' => true,
            'comment' => app::get('syspromotion')->_('商品关联的平台叶子节点分类ID'),
        ),
        'title' => array(
            'type' => 'string',
            'length' => 60,
            'required' => true,
            'comment' => app::get('syspromotion')->_('商品名称'),
        ),
        'image_default_id' => array(
            'type' => 'string',
            'comment' => app::get('syspromotion')->_('商品图片'),
        ),
        'price' => array(
            'type' => 'money',
            'required' => true,
            'label' => app::get('syspromotion')->_('商品价格'),
            'comment' => app::get('syspromotion')->_('商品价格'),
        ),
        'promotion_tag' => array(
            'type' => 'string',
            'length' => 10,
            'default' => 0,
            'required' => true,
            'label' => app::get('syspromotion')->_('促销标签'),
        ),
        'canuse_start_time' => array(
            'type' => 'time',
            'default'=> 0,
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
            'filterdefault'=>true,
            'label' => app::get('syspromotion')->_('起始可使用时间'),
            'comment' => app::get('syspromotion')->_('起始可使用时间'),
        ),
        'canuse_end_time' => array(
            'type' => 'time',
            'default'=> 0,
            'editable' => true,
            'in_list' => true,
            'default_in_list' => false,
            'filterdefault'=>true,
            'label' => app::get('syspromotion')->_('截止可使用时间'),
            'comment' => app::get('syspromotion')->_('截止可使用时间'),
        ),
        'status' => array(
            'type' => 'bool',
            'default' => '0',
            'required' => true,
            'label' => app::get('syspromotion')->_('促销状态'),
            'comment' => app::get('syspromotion')->_('促销状态'),
        ),
    ),

    'primary' => ['coupon_id', 'item_id'],
    'comment' => app::get('syspromotion')->_('商品与促销规则关联表'),
);

