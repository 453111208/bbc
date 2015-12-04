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
        'aftersales_bn' =>
        array(
            //'type' => 'bigint unsigned',
            'type' => 'bigint',
            'unsigned' => true,

            //'pkey' => true,
            'required' => true,
            'label' => app::get('sysaftersales')->_('申请售后编号'),
        ),
        'user_id' =>
        array(
            'type' => 'table:account@sysuser',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysaftersales')->_('申请会员'),
        ),
        'shop_id' =>
        array(
            'type' => 'table:shop@sysshop',
            'label' => app::get('sysaftersales')->_('所属商家'),
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysaftersales')->_('店铺ID'),
        ),
        'aftersales_type' =>
        array(
            'type' => array(
                'ONLY_REFUND' => app::get('sysaftersales')->_('仅退款'),
                'REFUND_GOODS' => app::get('sysaftersales')->_('退货退款'),
                'EXCHANGING_GOODS' => app::get('sysaftersales')->_('换货'),
            ),
            'default' => 'ONLY_REFUND',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysaftersales')->_('售后服务类型'),
        ),
        'progress' =>
        array(
            'type' => array(
                '0' => app::get('sysaftersales')->_('等待商家处理'),
                '1' => app::get('sysaftersales')->_('商家接受申请，等待消费者回寄'),
                '2' => app::get('sysaftersales')->_('消费者回寄，等待商家收货确认'),
                '3' => app::get('sysaftersales')->_('商家已驳回'),
                '4' => app::get('sysaftersales')->_('商家已处理'),//换货的时候可以直接在商家处理结束
                '5' => app::get('sysaftersales')->_('商家确认收货，同意退款,提交到平台,等待平台处理'),
                '6' => app::get('sysaftersales')->_('平台驳回退款申请'),
                '7' => app::get('sysaftersales')->_('平台已处理退款申请'),//退款，退货则需要平台确实退款
            ),
            'default' => '0',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysaftersales')->_('处理进度'),
        ),
        'status' =>
        array(
            'type' => array(
                '0' => app::get('sysaftersales')->_('待处理'),
                '1' => app::get('sysaftersales')->_('处理中'),
                '2' => app::get('sysaftersales')->_('已处理'),
                '3' => app::get('sysaftersales')->_('已驳回'),
            ),
            'default' => '0',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysaftersales')->_('状态'),
        ),
        'tid'=>
        array(
            'type' => 'table:trade@systrade',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysaftersales')->_('订单编号'),
        ),
        'oid'=>
        array(
            'type' => 'table:order@systrade',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysaftersales')->_('子订单编号'),
        ),
        'title' => array(
            //'type' => 'varchar(60)',
            'type' => 'string',
            'length' => 60,
            'required' => true,
            'default' => '',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysaftersales')->_('商品标题'),
        ),
        'num' => array(
            'type' => 'number',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysaftersales')->_('申请售后商品数量'),
        ),
        'reason' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysaftersales')->_('申请售后原因'),
        ),
        'description' =>
        array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'comment' => app::get('sysaftersales')->_('申请描述'),
        ),
        'evidence_pic' =>
        array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'length' => 5120,
            'comment' => app::get('sysaftersales')->_('图片凭证信息'),
        ),
        'shop_explanation' =>
        array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'comment' => app::get('sysaftersales')->_('商家处理申请说明'),
        ),
        'admin_explanation' =>
        array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'comment' => app::get('sysaftersales')->_('平台处理申请说明'),
        ),
        'sendback_data' =>
        array(
            'type' => 'serialize',
            'comment' => app::get('sysaftersales')->_('消费者提交退货物流信息'),
        ),
        'sendconfirm_data' =>
        array(
            'type' => 'serialize',
            'comment' => app::get('sysaftersales')->_('商家重新发货物流信息'),
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
    ),
    'primary' => 'aftersales_bn',
    'comment' => app::get('sysaftersales')->_('售后申请'),
);
