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
        'app_id' =>
        array (
            //'type' => 'varchar(32)',
            'type' => 'string',
            'length' => 32,
            'required' => true,
            //'pkey' => true,
            'width' => 100,
            'label' => app::get('base')->_('程序目录'),
            'hidden' => 1,
            'editable' => false,
            'in_list' => true,
            'default_in_list' => false,
        ),
        'app_name' => array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
        
            'width' => 150,
            'label' => app::get('base')->_('应用程序'),
            'is_title'=>1,
            'in_list' => true,
            'default_in_list' => 1),
        'debug_mode' => array (
            'type' => 'bool',
            'default' => 0,
            'width' => 100,
            'label' => app::get('base')->_('调试模式'),
            'in_list' => true,
            'default_in_list' => false ),
        'app_config' => array (
            'type' => 'text'),
        'status' =>  array (
            'label' => app::get('base')->_('状态'),
            'width' => 100,
            'default' => 'uninstalled',
            'type' => 
            array (
                'installed' => app::get('base')->_('已安装, 未启动'),
                'resolved' => app::get('base')->_('已配置'),
                'starting' => app::get('base')->_('正在启动'),
                'active' => app::get('base')->_('运行中'),
                'stopping' => app::get('base')->_('正在关闭'),
                'uninstalled' => app::get('base')->_('尚未安装'),
                'installing' => app::get('base')->_('正在安装'),
                'broken' => app::get('base')->_('已损坏'),
                'paused' => app::get('base')->_('已暂停'),
            ),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'webpath'=>array(
            //'type'=>'varchar(20)',
            'type'=>'string',
            'length'=>20,
            'comment' => app::get('base')->_('远程地址')),
        'description'=>array(
            'type'=>'varchar(255)',
            'type'=>'string',
            'length'=>255,
        
            'width' => 300,
            'label' => app::get('base')->_('说明'),
            'in_list' => true,
            'default_in_list' => 1),
        'local_ver'=>array(
            //'type'=>'varchar(20)',
            'type'=>'string',
            'length' => 20,
        
            'width' => 100,
            'label' => app::get('base')->_('当前版本'),
            'in_list' => true,
            'default_in_list' => 1),
        'remote_ver'=>array(
            //'type'=>'varchar(20)',
            'type' => 'string',
            'length' => 20,
        
            'width' => 100,
            'label' => app::get('base')->_('最新版本'),
            'in_list' => true,'default_in_list' => false),
        'author_name'=>array(
            //'type'=>'varchar(100)',
            'type' => 'string',
            'length' => 100,
        
            'comment' => app::get('base')->_('作者名')),
        'author_url'=>array(
            //'type'=>'varchar(100)',
            'type' => 'string',
            'length' => 100,
        
            'comment' => app::get('base')->_('作者url')),
        'author_email' => array(
            //'type'=>'varchar(100)',
            'type' => 'string',
            'length' => 100,
        
            'comment' => app::get('base')->_('作者邮件')),
        'dbver'=>array(
            //'type'=>'varchar(32)',
            'type' => 'string',
            'length' => 32,
        
            'comment' => app::get('base')->_('目前安装版本')),
        'remote_config'=>array(
            'type'=>'serialize',
            'comment' => app::get('base')->_('远程配置信息')),
    ),
    'primary' => 'app_id',
    'unbackup' => true,
    'comment' => app::get('base')->_('系统应用表'),

);


