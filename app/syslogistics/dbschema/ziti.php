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
        'id' =>
        array (
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('syslogistics')->_('自增ID'),
        ),
        'name' =>
        array (
            'type' => 'string',
            'label' => app::get('syslogistics')->_('自提点名称'),
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'filterdefault' => true,
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('syslogistics')->_('自提点名称'),
        ),
        'area_state_id' =>
        array (
            'type' => 'string',
            'required' => true,
            'comment' => app::get('syslogistics')->_('自提地区ID(省)'),
        ),
        'area_city_id' =>
        array (
            'type' => 'string',
            'required' => true,
            'comment' => app::get('syslogistics')->_('自提地区ID(城市)'),
        ),
        'area_district_id' =>
        array (
            'type' => 'string',
            'required' => true,
            'comment' => app::get('syslogistics')->_('自提地区ID(区,县)'),
        ),
        'area' =>
        array (
            'type' => 'string',
            'required' => true,
            'comment' => app::get('syslogistics')->_('地区ID'),
        ),
        'addr' =>
        array (
            'type' => 'string',
            'label' => app::get('syslogistics')->_('自提地址'),
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('syslogistics')->_('地址'),
        ),
        'tel' =>
        array (
            'type' => 'string',
            'length' => 50,
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'filterdefault' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('syslogistics')->_('联系方式'),
            'comment' => app::get('sysuser')->_('电话或者手机号码'),
        ),
    ),
    'primary' => 'id',
    'comment' => app::get('syslogistics')->_('自提点表'),
);
