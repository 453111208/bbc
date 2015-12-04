<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


return  array(
    'columns' => array(
        'tid' => array(
            //'type' => 'bigint unsigned',
            //'type' => 'bigint',
            'type' => 'table:trade',
            'required' => true,
            'comment' => app::get('systrade')->_('主订单id'),
            'label' => app::get('systrade')->_('主订单id'),
        ),
        'oid' => array(
            //'type' => 'bigint unsigned',
            //'type' => 'bigint',
            'type' => 'table:order',
            'required' => true,
            'comment' => app::get('systrade')->_('子订单id'),
            'label' => app::get('systrade')->_('子订单id'),
        ),
        'user_id' => array(
            //'type' => 'number',
            'type' => 'table:account@sysuser',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('systrade')->_('会员用户名'),
            'comment' => app::get('systrade')->_('会员id'),
        ),
        'promotion_id' => array(
            //'type' => 'int(8)',
            //'type' => 'number',
            'type' => 'table:promotions@syspromotion',
            'required' => true,
            'comment' => app::get('systrade')->_('促销规则id'),
            'label' => app::get('systrade')->_('促销规则id'),
        ),
        'item_id' => array(
            //'type' => 'number',
            'type' => 'table:item@sysitem',
            'comment' => app::get('systrade')->_('商品的ID'),
            'label' => app::get('systrade')->_('商品的ID'),
        ),
        'sku_id' => array(
            //'type' => 'number',
            'type' => 'table:sku@sysitem',
            'comment' => app::get('systrade')->_('sku的ID'),
            'label' => app::get('systrade')->_('sku的ID'),
        ),
        'promotion_type' => array(
            //'type' => 'varchar(10)',
            'type' => 'string',
            'length' => 30,
            'default' => '',
            'required' => true,
            'comment' => app::get('systrade')->_('优惠规则类型'),
            'label' => app::get('systrade')->_('优惠规则类型'),
        ),
        // 'discount_fee' => array(
        //   'type' => 'money',
        //   'default' => '0',
        //   'required' => true,
        //   'comment' => app::get('systrade')->_('优惠的订单总金额'),
        //   'label' => app::get('systrade')->_('优惠的金额'),
        // ),
        'promotion_tag' => array(
            //'type' => 'varchar(10)',
            'type' => 'string',
            'length' => 30,
            'comment' => app::get('systrade')->_('促销标签'),
            'label' => app::get('systrade')->_('促销标签'),
        ),
        'promotion_name' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'comment' => app::get('systrade')->_('促销名称'),
            'label' => app::get('systrade')->_('促销名称'),
        ),
        'promotion_desc' => array(
            'type' => 'text',
            'comment' => app::get('systrade')->_('促销描述'),
            'label' => app::get('systrade')->_('促销描述'),
        ),
    ),
    
    'comment' => app::get('systrade')->_('订单使用的促销信息表'),
);
