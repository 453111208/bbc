<?php
return array(
    'columns' => array(
        'user_id' => array(
            'type' => 'table:user',
            'in_list' => false,
            //'pkey' => true,
            'required' => true,
            'default_in_list' => false,
            'label' => app::get('sysuser')->_('会员'),
        ),
        'point_count' => array(
            'type' => 'number',
            'in_list' => true,
            'default_in_list' => true,
            'default' => 0,
            'comment' => app::get('sysuser')->_('会员总积分值'),
            'label' => app::get('sysuser')->_('会员总积分值'),
        ),
        'expired_point' => array(
            'type' => 'number',
            'in_list' => true,
            'default_in_list' => true,
            'default' => 0,
            'comment' => app::get('sysuser')->_('将要过期积分'),
            'label' => app::get('sysuser')->_('将要过期积分'),
        ),
        'modified_time'=> array(
            'type' => 'last_modify',
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysuser')->_('记录时间'),
            'label' => app::get('sysuser')->_('记录时间'),
        ),
    ),
    'primary' => 'user_id',
    'comment' => app::get('sysuser')->_('会员积分表'),
);
