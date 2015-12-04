<?php
return  array(
    'columns'=> array(
        'shop_id' => array(
            'type' => 'string',
            'required' => true,
            'comment' => app::get('sysopen')->_('对应的商户id'),
            'in_list' => true,
            'is_title' => true,
            'label' => app::get('sysopen')->_('商户id'),
            'default_in_list' => true,
            'width' => '30',
            'order' => 10,
        ),
        'key' => array(
            'type' => 'string',
            'label' => app::get('sysopen')->_('访问api时用的key'),
            'comment' => app::get('sysopen')->_('访问api时用的key'),
            'in_list' => true,
            'is_title' => true,
            'default_in_list' => true,
            'width' => '30',
            'order' => 10,
        ),
        'secret' => array(
            'type' => 'string',
            'label' => app::get('sysopen')->_('访问api时用的secret'),
            'comment' => app::get('sysopen')->_('访问api时用的secret'),
        ),
        'contact_type' => array(
            'type' => array(
                'notallowopen' => '禁止接入',
                'applyforopen' => '申请接入',
                'openstandard' => '标准接入',
            ),
            'required' => true,
            'label' => app::get('sysopen')->_('商户状态'),
            'comment' => app::get('sysopen')->_('商户状态'),
            'in_list' => true,
            'is_title' => true,
            'default_in_list' => true,
            'width' => '30',
            'order' => 10,
        ),
        'mark' => array(
            'type' => 'string',
            'label' => app::get('sysopen')->_('备注'),
            'comment' => app::get('sysopen')->_('备注'),
            'in_list' => true,
            'is_title' => true,
            'default_in_list' => true,
            'width' => '60',
            'order' => 10,
        ),
    ),

    'primary' => 'shop_id',
    'comment' => app::get('sysopen')->_('店铺请求开放api的key和secret'),
);

