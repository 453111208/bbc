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
        'refunds_id' =>
        array(
            'type' => 'number',
            'autoincrement' => true,
            //'pkey' => true,
            'required' => true,
            'comment' => app::get('sysaftersales')->_('退款申请ID'),
        ),
        'aftersales_bn' =>
        array(
            //'type' => 'bigint unsigned',
            'type' => 'bigint',
            'unsigned' => true,
            
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysaftersales')->_('申请售后编号'),
        ),
        'refunds_type' =>
        array(
            'type' => array(
                '0' => '售后申请退款',
                '1' => '商家取消异常订单退款',
            ),
            'default' => '0',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysaftersales')->_('退款类型'),
        ),
        'status' =>
        array(
            'type' => array(
                '0' => '待处理',
                '1' => '已处理',
                '2' => '已驳回',
            ),
            'default' => '0',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysaftersales')->_('审核状态'),
        ),
        'refunds_reason' =>
        array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysaftersales')->_('申请退款理由'),
        ),
        'total_price' =>
        array(
            'type' => 'money',
            'default' => '0',
            'in_list' => true,
            'default_in_list' => true,
            'label' => '退款总金额',
        ),
        'created_time' =>
        array(
            'type' => 'time',
            'label' => app::get('sysaftersales')->_('创建时间'),
            'width' => '100',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'modified_time' => array(
            'type' => 'last_modify',
            'label' => app::get('sysaftersales')->_('修改时间'),
            'in_list' => true,
            'default_in_list' => true,
            'width' => '100',
        ),
        'user_id' => array(
            'type' => 'number',
            'comment' => '会员id',
        ),
        'shop_id' => array(
            'type' => 'number',
            'comment' => '店铺id',
        ),
        'tid' => array(
            'type' => 'serialize',
            'comment' => '该退款单的主订单号',
        ),
        'oid' => array(
            'type' => 'serialize',
            'comment' => '该退款单的订单号',
        ),
    ),
    'primary' => 'refunds_id',
    'comment' => app::get('sysaftersales')->_('退款申请表'),
);

