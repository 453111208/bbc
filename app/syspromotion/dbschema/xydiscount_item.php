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
        'xydiscount_id' => array(
            'type' => 'number',
            //'pkey' => true,
            'required' => true,
            'comment' => app::get('syspromotion')->_('X件Y折ID'),
        ),
        'item_id' => array(
            'type' => 'number',
            //'pkey' => true,
            'required' => true,
            'comment' => app::get('syspromotion')->_('商品ID'),
        ),
        'shop_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => app::get('syspromotion')->_('所属商家'),
            'comment' => app::get('syspromotion')->_('所属商家的店铺id'),
        ),
        'leaf_cat_id' => array(
            'type' => 'number',
            'required' => true,
            'comment' => app::get('syspromotion')->_('商品关联的平台叶子节点分类ID'),
        ),
        'title' => array(
            //'type' => 'varchar(60)',
            'type' => 'string',
            'length' => 60,
            'required' => true,
            'comment' => app::get('syspromotion')->_('商品名称'),
        ),
        'image_default_id' => array(
            //'type' => 'varchar(32)',
            'type' => 'string',
            'length' => 255,
            'comment' => app::get('syspromotion')->_('商品图片'),
        ),
        'price' => array(
            'type' => 'money',
            'required' => true,
            'label' => app::get('syspromotion')->_('商品价格'),
            'comment' => app::get('syspromotion')->_('商品价格'),
        ),
        'promotion_tag' => array(
            //'type' => 'varchar(10)',
            'type' => 'string',
            'length' => 10,
            'default' => 0,
            'required' => true,
            'label' => app::get('syspromotion')->_('促销标签'),
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
        'status' => array(
            'type' => 'bool',
            'default' => '0',
            'required' => true,
            'label' => app::get('syspromotion')->_('是否生效中'),
            'comment' => app::get('syspromotion')->_('是否生效中'),
        ),
    ),
    
    'primary' => ['xydiscount_id', 'item_id'],
    'comment' => app::get('syspromotion')->_('商品与促销规则关联表'),
);
