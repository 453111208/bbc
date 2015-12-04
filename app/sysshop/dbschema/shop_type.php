<?php

/**
 * ShopEx LuckyMall
 *
 * @author     ajx
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array(
    'columns' => array(
        'shoptype_id' => array(
            //'type'=>'int(2)',
            'type' => 'smallint',
            'length' => 80,
            'label' => 'id',
            'comment' => app::get('sysshop')->_('自增id'),
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'order' => 1,
        ),
        'shop_type' => array(
            'type' => array(
                'flag'=>'品牌旗舰店',
                'brand'=>'品牌专卖店',
                'cat'=>'类目专营店',
                'self'=>'运营商自营店铺',
            ),
            'label' => app::get('sysshop')->_('店铺类型id'),
            'comment' => app::get('sysshop')->_('店铺类型id'),
            'required' => true,
           'length' => 20,
        ),
        'name' => array(
            //'type'=>'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'label' => app::get('sysshop')->_('店铺类型名称'),
            'comment' => app::get('sysshop')->_('店铺类型名称'),
            'required' => true,
            'in_list' => true,
            'is_title' => true,
            'default_in_list'=>true,
            'order' => 5,
        ),
        'status' => array(
            'type'=>'bool',
            'label' => app::get('sysshop')->_('状态'),
            'comment' => app::get('sysshop')->_('状态'),
            'required' => true,
            'is_title' => true,
            'in_list' => true,
            'default_in_list'=>true,
            'order' => 6,
        ),
        'is_exclusive' => array(
            'type'=>'bool',
            'default' => 0,
            'label' => app::get('sysshop')->_('是否排他'),
            'comment' => app::get('sysshop')->_('是否排他'),
            'required' => true,
            'is_title' => true,
            'in_list' => true,
            'default_in_list'=>true,
            'order' => 7,
        ),
        'brief' => array(
            //'type'=>'varchar(500)',
            'type' => 'string',
            'length' => 500,
            'label' => app::get('sysshop')->_('类型描述'),
            'comment' => app::get('sysshop')->_('类型描述'),
            'required' => true,
            'is_title' => true,
            'in_list' => false,
            'default_in_list'=>false,
            'order' => 10,
        ),
        'suffix' => array(
            //'type'=>'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'label' => app::get('sysshop')->_('店铺名称后缀'),
            'comment' => app::get('sysshop')->_('店铺名称后缀'),
            'required' => true,
            'is_title' => true,
            'in_list' => true,
            'default_in_list'=>false,
            'order' => 8,
        ),
        'max_item' => array(
            //'type'=>'int(10)',
            'type' => 'number',
            'label' => app::get('sysshop')->_('店铺默认商品上限'),
            'comment' => app::get('sysshop')->_('店铺默认商品上限'),
            'required' => true,
            'is_title' => true,
            'in_list' => true,
            'default_in_list'=>true,
            'order' => 9,
        ),
        'is_display' => array(
            'type' =>'bool',
            'label' => app::get('sysshop')->_('是否显示'),
            'default' => 1,
        ),
    ),
    'primary' => 'shoptype_id',
    'index' => array(
        'ind_shop_type' => ['columns' => ['shop_type']],
    ),
    'comment' => app::get('sysshop')->_('店铺类型表'),
);

