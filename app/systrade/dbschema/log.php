<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return  array(
    'columns' => array(
        'log_id' => array(
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('systrade')->_('订单日志ID'),
        ),
        'rel_id' => array(
            //'type' => 'bigint unsigned',
            'type' => 'bigint',
            'unsigned' => true,
            
            'required' => true,
            'default' => 0,
            'editable' => false,
            'label' => app::get('systrade')->_('单据对象ID'),
            'comment' => app::get('systrade')->_('单据对象ID,如订单号，退款单号等'),
        ),
        'op_id' => array(
            'type' => 'number',
            'label' => app::get('systrade')->_('操作员'),
            'width' => 110,
            'editable' => false,
            'filtertype' => 'normal',
            'in_list' => true,
            'comment' => app::get('systrade')->_('操作员ID'),
        ),
        'op_name' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'label' => app::get('systrade')->_('操作人名称'),
            'width' => 110,
            'editable' => false,
            'filtertype' => 'normal',
            'filterdefault' => true,
            'in_list' => true,
        ),
        'op_role' => array(
            'type' => array(
                'buyer' => app::get('systrade')->_('购买者'),
                'seller' => app::get('systrade')->_('卖家'),
                'shopadmin' => app::get('systrade')->_('平台操作员'),
                'system' => app::get('systrade')->_('系统'),
            ),
            'default' => 'system',
            'required' => true,
            'label' => app::get('systrade')->_('操作角色'),
            'width' => 110,
            'editable' => false,
            'filtertype' => 'yes',
            'filterdefault' => true,
            'in_list' => true,
            'comment' => app::get('systrade')->_('操作角色'),
        ),
        'behavior' => array(
            'type' => array(
                'create' => app::get('systrade')->_('创建'),
                'update' => app::get('systrade')->_('修改'),
                'payed' => app::get('systrade')->_('支付'),
                'delivery' => app::get('systrade')->_('发货'),
                'confirm' => app::get('systrade')->_('收货'),
                'cancel' => app::get('systrade')->_('取消'),
                'refund' => app::get('systrade')->_('退款'),
                'reship' => app::get('systrade')->_('退货'),
                'exchange' => app::get('systrade')->_('换货'),
                'mark' => app::get('systrade')->_('修改备注'),
                'finish' => app::get('systrade')->_('完成'),
            ),
            'default' => 'update',
            'required' => true,
            'label' => app::get('systrade')->_('操作行为'),
            'width' => 110,
            'editable' => false,
            'filtertype' => 'yes',
            'filterdefault' => true,
            'in_list' => true,
            'comment' => app::get('systrade')->_('日志记录操作的行为'),
        ),
        'log_text' => array(
            'type' => 'text',
            'editable' => false,
            'in_list' => true,
            'default_in_list' => false,
            'comment' => app::get('systrade')->_('操作内容'),
        ),
        'log_time' => array(
            'type' => 'time',
            'label' => app::get('systrade')->_('记录时间'),
            'width' => 110,
            'editable' => false,
            'filtertype' => 'time',
            'filterdefault' => true,
            'in_list' => true,
            'comment' => app::get('systrade')->_('记录时间'),
        ),
    ),
    'primary' => 'log_id',
    'comment' => app::get('systrade')->_('订单日志表'),
);
