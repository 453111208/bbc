<?php
return  array(
    'columns' => array(
        'experience_id' => array(
            'type' => 'number',
            //'pkey' => true,
            'autoincrement' => true,
            'label' => app::get('sysuser')->_('经验值记录id'),
        ),
        'user_id' => array(
            'type' => 'table:user',
            'in_list' => false,
            'default_in_list' => false,
            'label' => app::get('sysuser')->_('会员'),
        ),
        'modified_time' => array(
            'type' => 'last_modify',
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysuser')->_('记录时间'),
            'label' => app::get('sysuser')->_('记录时间'),
        ),
        'behavior_type' => array(
            'type' => array(
                'obtain' => app::get('sysuser')->_('获得'),
                'consume' => app::get('sysuser')->_('消费'),
            ),
            'in_list' => true,
            'default_in_list' => true,
            'default' => 'obtain',
            'label' => app::get('sysuser')->_('行为类型'),
            'comment' => app::get('sysuser')->_('行为类型'),
        ),
        'behavior' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysuser')->_('行为描述'),
            'label' => app::get('sysuser')->_('行为描述'),
        ),
        'experience' => array(
            //'type' => 'int',
            'type' => 'number',
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysuser')->_('成长值'),
            'label' => app::get('sysuser')->_('成长值'),
        ),
        'remark' => array(
            //'type' => 'varchar(500)',
            'type' => 'string',
            'length' => 500,
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysuser')->_('备注(记录订单号)'),
            'label' => app::get('sysuser')->_('备注'),
        ),
        'expiration_time' => array(
            'type' => 'time',
            'in_list' => false,
            'default_in_list' => false,
            'default' => 0,
            'comment' => app::get('sysuser')->_('经验值过期时间'),
            'label' => app::get('sysuser')->_('经验值过期时间'),
        ),
    ),
    
    'primary' => 'experience_id',
    'comment' => app::get('sysuser')->_('会员经验值详细记录表'),
);
