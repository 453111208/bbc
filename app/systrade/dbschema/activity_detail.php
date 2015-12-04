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
        'activity_id' => array(
            //'type' => 'int(8)',
            //'type' => 'number',
            'type' => 'table:activity@syspromotion',
            'required' => true,
            'comment' => app::get('systrade')->_('活动规则id'),
            'label' => app::get('systrade')->_('活动规则id'),
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
        // 'discount_fee' => array(
        //   'type' => 'money',
        //   'default' => '0',
        //   'required' => true,
        //   'comment' => app::get('systrade')->_('优惠的订单总金额'),
        //   'label' => app::get('systrade')->_('优惠的金额'),
        // ),
        'activity_tag' => array(
            //'type' => 'varchar(10)',
            'type' => 'string',
            'length' => 30,
            'comment' => app::get('systrade')->_('活动标签'),
            'label' => app::get('systrade')->_('活动标签'),
        ),
        'activity_name' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'comment' => app::get('systrade')->_('活动名称'),
            'label' => app::get('systrade')->_('活动名称'),
        ),
        'activity_desc' => array(
            'type' => 'text',
            'comment' => app::get('systrade')->_('活动描述'),
            'label' => app::get('systrade')->_('活动描述'),
        ),
    ),
    
    'comment' => app::get('systrade')->_('订单使用的活动信息表'),
);
