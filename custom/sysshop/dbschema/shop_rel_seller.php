<?php
return array(
    'columns' => array(
        'shop_id' => array(
            'type' => 'table:shop',
            //'pkey' => true,
            'required' => true,
            'comment' => app::get('sysshop')->_('关联企业'),
        ),
        'seller_id' => array(
            'type' => 'table:seller',
            //'pkey'=>true,
            'required' => true,
            'comment' => app::get('sysshop')->_(' 关联企业会员id'),
        ),
        'roles' => array(
            'type' => array(
                'admin' => '超级管理员',
                'member' => '普通管理员',
            ),
            'default' => 'admin',
            'required' => true,
            'comment' => app::get('sysshop')->_('企业会员角色'),
        ),
        'authority' => array(
            'type' => array(
                'all' => '所有权限',
                'other' => '其他权限',
            ),
            'default' => 'all',
            'required' => true,
            'comment' => app::get('sysshop')->_('企业会员权限'),
        ),
        'shop_name' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'comment' => app::get('sysshop')->_('企业所属企业名称'),
        ),
    ),
    
    'primary' => ['shop_id', 'seller_id'],
);
