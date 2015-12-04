<?php
return  array(
    'columns'=>array(
        'stat_id'=>array(
            //'type'=>'bigint unsigned',
            'type' => 'bigint',
            'unsigned' => true,
            //'pkey'=>true,
            'autoincrement' => true,
            'required' => true,
            'label' => 'id',
            'comment' => app::get('sysstat')->_('商家数据统计id 自赠'),
            'order' => 1,
        ),
        'shop_id' => array(
            'type' => 'table:shop@sysshop',
            'label' => app::get('sysstat')->_('所属店铺'),
            'comment' => app::get('sysstat')->_('店铺id'),
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
            'filtertype' => 'yes',
            'filterdefault' => true,
            'order' => 11,
        ),
        'new_trade'=>array(
            //'type'=>'varchar(50)',
            'type' => 'number',
            'default' => 0,
            'label' => app::get('sysstat')->_('新增订单数'),
            'comment' => app::get('sysstat')->_('新增订单数'),
            'order' => 2,
        ),
        'new_fee'=>array(
            'type' => 'money',
            'default' => 0,
            'comment' => app::get('sysstat')->_('新增订单额'),
            
        ),
        'ready_trade'=>array(
            //'type'=>'varchar(50)',
            'type' => 'number',
            'default' => 0,
            'label' => app::get('sysstat')->_('待付款订单数'),
            'comment' => app::get('sysstat')->_('待付款订单数'),
            'order' => 3,
        ),
        'ready_fee'=>array(
            'type' => 'money',
            'default' => 0,
            'comment' => app::get('sysstat')->_('待付款订单额'),
            
        ),
        'alreadytrade'=>array(
            //'type'=>'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'default' => 0,
            'label' => app::get('sysstat')->_('以付款订单数'),
            'comment' => app::get('sysstat')->_('以付款订单数'),
            'order' => 3,
        ),
        'alreadyfee'=>array(
            'type' => 'money',
            'default' => 0,
            'comment' => app::get('sysstat')->_('以付款订单额'),
            
        ),
        'ready_send_trade'=>array(
            //'type'=>'varchar(50)',
            'type' => 'number',
            'default' => 0,
            'label' => app::get('sysstat')->_('待发货订单数量'),
            'comment' => app::get('sysstat')->_('待发货订单数量'),
            'order' => 5,
        ),
        'ready_send_fee'=>array(
            'type' => 'money',
            'default' => 0,
            'comment' => app::get('sysstat')->_('待发货订单额'),
            
        ),
        'already_send_trade'=>array(
            //'type'=>'varchar(50)',
            'type' => 'number',
            'length' => 50,
            'default' => 0,
            'label' => app::get('sysstat')->_('待收货订单数量'),
            'comment' => app::get('sysstat')->_('待收货订单数量'),
            'order' => 6,
        ),
        'already_send_fee'=>array(
            'type' => 'money',
            'default' => 0,
            'comment' => app::get('sysstat')->_('待收货订单额'),
        ),
        'cancle_trade'=>array(
            //'type'=>'varchar(50)',
            'type' => 'number',
            'default' => 0,
            'label' => app::get('sysstat')->_('已取消的订单数量'),
            'comment' => app::get('sysstat')->_('已取消的订单数量'),
            'order' => 7,
        ),
        'cancle_fee'=>array(
            'type' => 'money',
            'default' => 0,
            'comment' => app::get('sysstat')->_('已取消的订单额'),
        ),
        'complete_trade'=>array(
            //'type'=>'varchar(50)',
            'type' => 'number',
            'default' => 0,
            'label' => app::get('sysstat')->_('已完成的订单数量'),
            'comment' => app::get('sysstat')->_('已完成的订单数量'),
            'order' => 8,
        ),
        'complete_fee'=>array(
            'type' => 'money',
            'default' => 0,
            'comment' => app::get('sysstat')->_('已完成订单额'),
        ),
        'createtime'=>array(
            'type'=>'time',
            'comment' => app::get('sysstat')->_('创建时间'),
        ),
    ),
    
    'primary' => 'stat_id',
);
