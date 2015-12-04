<?php
//异常订单表
return  array(
    'columns' => array(
        'id' => array(
            //'type' => 'bigint unsigned',
            'type' => 'bigint',
            'unsigned' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'required' => true,
            'comment' => app::get('systrade')->_('id'),
        ),
        'tid' => array(
            'type' => 'table:trade',
            'required' => true,
            'label' => app::get('systrade')->_('订单编号'),
            'in_list' => true,
            'default_in_list' => true,
            'width' => 100,
            'order' => 11,
        ),
        'reason' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('systrade')->_('取消原因'),
        ),
        'status' => array(
            'type' => array(
                'WAIT_PROCESSING' => '等待平台处理',
                'FINISHED' => '平台已处理',
                'CLOSED' => '平台已驳回',
            ),
            'default' => 'WAIT_PROCESSING',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('systrade')->_('状态'),
        ),
        'reject_reason' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'length' => 100,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('systrade')->_('平台驳回原因'),
        ),
        'created_time' => array(
            'type' => 'last_modify',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '100',
            'order' => 18,
            'label' => app::get('systrade')->_('创建时间'),
        ),
        'modified_time' => array(
            'type' => 'last_modify',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('systrade')->_('最后更新时间'),
        ),
    ),
    
    'primary' => 'id',
    'comment' => app::get('systrade')->_('取消异常订单表'),
);
