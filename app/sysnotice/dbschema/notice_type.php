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
        'type_id' =>
        array (
            'type' => 'number',
            'required' => true,
            // 'pkey' => true,
            'autoincrement' => true,
            'comment' => app::get('sysnotice')->_('公告类型ID'),
        ),
        'type_name' =>
        array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'label' => app::get('sysnotice')->_('公告类型名称'),
            'required' => true,
            'comment' => app::get('sysnotice')->_('公告类型名称'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'fabu_time' =>
        array (
            'type' => 'time',
            'label' => app::get('sysnotice')->_('发布时间'),
            'comment' => app::get('sysnotice')->_('发布时间'),
            'in_list' => true,
            'default_in_list' => true,
        ),

    ),
    'primary' => 'type_id',
    'comment' => app::get('sysnotice')->_('公告类型表'),
);
