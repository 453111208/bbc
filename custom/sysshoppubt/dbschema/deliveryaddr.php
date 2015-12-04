<?php
// 发布交易 的交割地址
return array (
    'columns' =>
    array (
        'deliveryaddr_id' =>
        array (
            //'type' => 'int(10)',
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('会员地址ID'),
        ),

        'uniqid' =>
        array (
            'type' => 'string',
            'length' => 190,
            'comment' => app::get('sysshoppubt')->_('标准商品发布表uniqid'),
        ),
        
        'transport_way'=>
         array (
            'type' => 'string',
            'length' => 100,
            'default' => 0,
            'required' => true,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('运输方式'),
        ),

      'area' =>
        array (
            'type' => 'string',
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('地区'),
        ),
        'addr' =>
        array (
            'type' => 'string',
            'length' => 100,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('地址'),
        ),
        'zip' =>
        array (
            'type' => 'string',
            'length' => 20,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('邮编'),
        ),
      'name' =>
        array (
            'is_title' => true,
            'type' => 'string',
            'length' => 50,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('联系人'),
        ),
        'tel' =>
        array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('电话'),
        ),
        'mobile' =>
        array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('手机'),
        ),
        'def_addr' =>
        array (
            'type' => 'bool',
            'default' => 0, 
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('默认地址'),
        ),

        'create_time' => array(
            'type' => 'time',
            'comment' => app::get('sysshoppubt')->_('创建时间'),
        ),
    ),
    'primary' => 'deliveryaddr_id',
    'comment' => app::get('sysshoppubt')->_('发布交易的交割地址'),
);
