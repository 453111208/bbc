<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array (
    'columns' =>
    array (
        'addr_id' =>
        array (
            //'type' => 'int(10)',
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('sysuser')->_('会员地址ID'),
        ),
        'user_id' =>
        array (
            'type' => 'table:user',
            'default' => 0,
            'required' => true,
            'editable' => false,
            'comment' => app::get('sysuser')->_('会员ID'),
        ),
        'name' =>
        array (
            'is_title' => true,
            'type' => 'string',
            'length' => 50,
            'editable' => false,
            'comment' => app::get('sysuser')->_('会员名称'),
        ),
    
        'area' =>
        array (
            'type' => 'string',
            'editable' => false,
            'comment' => app::get('sysuser')->_('地区'),
        ),
        'addr' =>
        array (
            'type' => 'string',
            'length' => 100,
            'editable' => false,
            'comment' => app::get('sysuser')->_('地址'),
        ),
        'zip' =>
        array (
            'type' => 'string',
            'length' => 20,
            'editable' => false,
            'comment' => app::get('sysuser')->_('邮编'),
        ),
        'tel' =>
        array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'editable' => false,
            'comment' => app::get('sysuser')->_('电话'),
        ),
        'mobile' =>
        array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'editable' => false,
            'comment' => app::get('sysuser')->_('手机'),
        ),
        'def_addr' =>
        array (
            'type' => 'bool',
            'default' => 0, 
            'editable' => false,
            'comment' => app::get('sysuser')->_('默认地址'),
        ),
    ),
    'primary' => 'addr_id',
    'comment' => app::get('sysuser')->_('会员地址表'),
);
