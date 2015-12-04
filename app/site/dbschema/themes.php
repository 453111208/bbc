<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
return  array(
    'columns' => array(
        'theme' => array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'required' => true,
            'default' => '',
            //'pkey' => true,
            'editable' => false,
            'is_title' => true,
            'label'=>app::get('site')->_('目录'),
            'width'=>'90',
            'in_list'=>true,
            'default_in_list'=>true,
            'comment' => app::get('site')->_('主题唯一英文名称'),
        ),
        'name' => array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'editable' => false,
            'is_title'=>true,
            'label'=>app::get('site')->_('模板名称'),
            'width'=>'200',
            'in_list'=>true,
            'default_in_list'=>true,
            'comment' => app::get('site')->_('主题名称'),
        ),
        'platform' => array(
            'type' => array(
                'pc' => '电脑端',
                'wap' => '无线端',
            ),
            'default' => 'pc',
            'required' => true,
            'label' => app::get('site')->_('模板终端'),
            'comment' => app::get('site')->_('模板终端'),
        ),
        'stime' => array(
            //'type' => 'int unsigned',
            'type' => 'time',
            'editable' => false,
            'comment' => app::get('site')->_('开始使用时间'),
        ),
        'author' => array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            
            'editable' => false,
            'label'=>app::get('site')->_('作者'),
            'width'=>'100',
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'site' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'editable' => false,
            'label'=>app::get('site')->_('作者网址'),
            'width'=>'200',
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'version' => array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'editable' => false,
            'label'=>app::get('site')->_('版本'),
            'width'=>'80',
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'info' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'editable' => false,
            'comment' => app::get('site')->_('详细说明'),
        ),
        'config' => array(
            'type' => 'serialize',
            'editable' => false,
            'comment' => app::get('site')->_('配置信息'),
        ),
        'is_used' =>array(
            'type' => 'bool',
            'editable' => false,
            'default' => 0,
            'comment' => app::get('site')->_('是否启用'),
        ),
    ),
    'primary' => 'theme',
    'unbackup' => true,
    'comment' => app::get('site')->_('模板表'),
);
