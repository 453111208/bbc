<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2014-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array (
    'columns' =>
    array (
        'notice_id' =>
        array (
            'type' => 'number',
            'required' => true,
            // 'pkey' => true,
            'autoincrement' => true,
            'comment' => app::get('sysnotice')->_('公告ID'),
        ),
        'notice_name' =>
        array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'label' => app::get('sysnotice')->_('公告名称'),
            'required' => true,
            'editable' => true,
            'order' => 10,
            'comment' => app::get('sysnotice')->_('公告名称'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'notice_content' =>
        array (
            'type' => 'text',
            'label' => app::get('sysnotice')->_('公告内容'),
            'comment' => app::get('sysnotice')->_('公告内容'),
            'editable' => true,
        ),
        'notice_time' =>
        array (
            'type' => 'time',
            'label' => app::get('sysnotice')->_('公告时间'),
            'comment' => app::get('sysnotice')->_('公告时间'),
            'in_list' => true,
            'editable' => true,
            'default_in_list' => true,
        ),
        'type_id' =>
        array (
            'type' => 'number',
            'default' => 0,
            'comment' => app::get('sysnotice')->_('公告类型ID'),
        ),
         'image_default_id' => array(
            //'type' => 'varchar(32)',
            'type' => 'string',
            // 'required' => true,
            'comment' => app::get('sysnotice')->_('公告默认图'),
        ),
    ),
    'primary' => 'notice_id',
    'comment' => app::get('sysnotice')->_('公告表'),
); 
