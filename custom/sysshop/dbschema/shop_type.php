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
                '0'=>'平台方类型',
                '1'=>'自定义类型',
            ),
            'label' => app::get('sysshop')->_('企业类型来源'),
            'comment' => app::get('sysshop')->_('企业类型来源'),
             'length' => 200,

        ),
        
        'name' => array(
            //'type'=>'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'label' => app::get('sysshop')->_('企业类型名称'),
            'comment' => app::get('sysshop')->_('企业类型名称'),
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
            'is_title' => true,
            'order' => 10,
        ),
        'suffix' => array(
            //'type'=>'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'label' => app::get('sysshop')->_('企业名称后缀'),
            'comment' => app::get('sysshop')->_('企业名称后缀'),

            'order' => 8,
        ),
        'max_item' => array(
            //'type'=>'int(10)',
            'type' => 'number',
            'label' => app::get('sysshop')->_('企业默认商品上限'),
            'comment' => app::get('sysshop')->_('企业默认商品上限'),
            'order' => 9,
        ),
        'is_display' => array(
            'type' =>'bool',
            'label' => app::get('sysshop')->_('是否显示'),
            'default' => 1,
        ),
        'use_type' => array(
            'type' => array(
                '0'=>'企业类型',
                '1'=>'所属行业',
                 '2'=>'公司性质',
                '3'=>'主要产品',
                 '4'=>'注册原因',
                 '5'=>'公司规模',
            ),
            'required' => true,
            'default' => 0,
            'in_list' => true,
            'default_in_list'=>true,
            'label' => app::get('sysshop')->_('类型简述'),
            'comment' => app::get('sysshop')->_('类型简述'),
            'required' => true,
        ),
    ),
    'primary' => 'shoptype_id',
    'index' => array(
        'ind_shop_type' => ['columns' => ['shop_type']],
    ),
    'comment' => app::get('sysshop')->_('企业类型表'),
);

