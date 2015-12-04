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
        'id' => array(
            'type' => 'number',
            //'pkey' => true,
            'autoincrement' => true,
            'comment' => app::get('base')->_('序号'),
        ),
        'prefix' => array(
            //'type'=>'varchar(255)',
            'type' => 'string',
            'required'=>true,
            'comment' => app::get('base')->_('kvstore类型'),
        ),
        'key' => array(
            //'type'=>'varchar(255)',
            'type' => 'string',
            'required'=>true,
            'comment' => app::get('base')->_('kvstore存储的键值'),
        ),
        'value' => array(
            'type'=>'serialize',
            'comment' => app::get('base')->_('kvstore存储值'),
        ),
        'dateline' => array(
            'type'=>'time',
            'comment' => app::get('base')->_('存储修改时间'),
        ),
        'ttl' => array(
            'type'=>'time',
            'default' => 0,
            'comment' => app::get('base')->_('过期时间,0代表不过期'),
        ),
    ),
    'primary' => 'id',
    'index' => array (
        'ind_prefix' => ['columns' => ['prefix']],
        'ind_key' => ['columns' => ['key']],
    ),
    
    'comment' => app::get('base')->_('kvstore存储表'),
);
