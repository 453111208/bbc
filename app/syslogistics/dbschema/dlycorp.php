<?php

/**
 * ShopEx b2b2c
 *
 * @author     ajx
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return  array(
    'columns' => array(
        'corp_id' => array(
            //'type'=>'int(2)',
            'type' => 'smallint',
            'label' => app::get('syslogistics')->_('物流公司ID'),
            'comment' => app::get('syslogistics')->_('物流公司ID'),
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            //'extra' => 'auto_increment',
            'order' => 1,
        ),
        'corp_code' => array(
            //'type'=>'varchar(200)',
            'type' => 'string',
            'length' => 200,
            'label' => app::get('syslogistics')->_('物流公司代码'),
            'comment' => app::get('syslogistics')->_('物流公司代码'), 
            'required' => true,
            'is_title' => true,
            'in_list' => true,
            'default_in_list'=>true,
            'order' => 5,
        ),
        'full_name' => array(
            //'type'=>'varchar(200)',
            'type' => 'string',
            'length' => 200,
            'label' => app::get('syslogistics')->_('物流公司全名'),
            'comment' => app::get('syslogistics')->_('物流公司全名'),  
            'in_list' => true,
            'is_title' => true,
            'default_in_list'=>false,
            'order' => 10,
        ),
        
        'corp_name' => array(
            //'type'=>'varchar(200)',
            'type' => 'string',
            'length' => 200,
            'label' => app::get('syslogistics')->_('物流公司简称'),
            'comment' => app::get('syslogistics')->_('物流公司简称'), 
            'required' => true,
            'is_title' => true,
            'in_list' => true,
            'default_in_list'=>true,
            'order' => 6,
        ),
        'website' => array(
            //'type'=>'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'label' => app::get('syslogistics')->_('物流公司网址'),
            'comment' => app::get('syslogistics')->_('物流公司网址'),
            'is_title' => true,
            'in_list' => true,
            'default_in_list'=>true,
            'order' => 7,
        ),
        'request_url' => array(
            //'type'=>'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'label' => app::get('syslogistics')->_('查询接口网址'),
            'comment' => app::get('syslogistics')->_('查询接口网址'),
            'is_title' => true,
            'in_list' => true,
            'default_in_list'=>false,
            'order' => 8,
        ),
        'order_sort' => array(
            //'type'=>'int(10)',
            'type' => 'number',
            
            'label' => app::get('syslogistics')->_('排序'),
            'comment' => app::get('syslogistics')->_('排序'),
            'required' => true,
            'default' => 0,
            'is_title' => true,
            'in_list' => true,
            'default_in_list'=>false,
            'order' => 9,
        ),
        'custom' => array(
            'type' => 'bool',
            'default' => 0,
        ),
    ),
    'primary' => 'corp_id',
    'index' => array(
        'ind_corp_code' => ['columns' => ['corp_code']],
    ),
    'comment' => app::get('syslogistics')->_('物流公司表'),
);
