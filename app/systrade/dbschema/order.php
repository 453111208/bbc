<?php
return  array(
    'columns'=>array(
        'oid'=>array(
            //'type'=>'bigint unsigned',
            'type' => 'bigint',
            'unsigned' => true,
            'required' => true,
            //'pkey' => true,
            'comment' => app::get('systrade')->_('子订单编号'),
        ),
        'tid' => array(
            'type' => 'table:trade',
            'required' => true,
            'comment' => app::get('systrade')->_('订单编号'),
        ),
        'shop_id' => array(
            'type' => 'table:shop@sysshop',
            'required' => true,
            'comment' => app::get('systrade')->_('所属商家'),
        ),
        'user_id' => array(
            'type' => 'table:account@sysuser',
            'required' => true,
            'comment' => app::get('systrade')->_('买家id'),
        ),
        'item_id' => array(
            'type' => 'table:item@sysitem',
            'required' => true,
            'comment' => app::get('systrade')->_('商品id'),
        ),
        'sku_id' => array(
            'type' => 'table:sku@sysitem',
            'required' => true,
            'comment' => app::get('systrade')->_('货品id'),
        ),
        'bn' => array (
            'type'=>'varchar(45)',
            'type' => 'string',
            'length' => 45,
            'editable' => false,
            'is_title' => true,
            'comment' => app::get('systrade')->_('明细商品的编码'),
        ),
        'title' => array(
            //'type' => 'varchar(60)',
            'type' => 'string',
            'length' => 90,
            'required' => true,
            'default' => '',
            'comment' => app::get('systrade')->_('商品标题'),
        ),
        'spec_nature_info' => array (
            'type' => 'text',
            'comment' => app::get('systrade')->_('sku描述'),
        ),
        'price' => array(
            'type' => 'money',
            'default' => '0',
            'comment' => app::get('systrade')->_('商品价格'),
        ),
        'num' => array(
            'type' => 'number',
            'comment' => app::get('systrade')->_('购买数量'),
        ),
        'sendnum' => array(
          'type' => 'float',
          'default' => 0,
          'required' => true,
          'editable' => false,
          'comment' => app::get('systrade')->_('明细商品发货数量'),
        ),
        'sku_properties_name' => array(
            //'type' => 'varchar(500)',
            'type' => 'string',
            'length' => 500,
            'comment' => app::get('systrade')->_('SKU的值'),
        ),
        'refund_id' => array(
            //'type' => 'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'comment' => app::get('systrade')->_('最近退款ID'),
        ),
        'is_oversold' => array(
            'type' => 'bool',
            'default' => 0,
            'comment' => app::get('systrade')->_('是否超卖'),
        ),
        'pay_time' => array(
            'type' => 'time',
            'comment' => app::get('systrade')->_('付款时间'),
        ),
        'end_time' => array(
            'type' => 'time',
            'comment' => app::get('systrade')->_('结束时间'),
        ),
        'consign_time' => array(
            'type' => 'time',
            'comment' => app::get('systrade')->_('发货时间'),
        ),
        'shipping_type' => array(
            //'type' => 'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'comment' => app::get('systrade')->_('运送方式'),
        ),
        'bind_oid' => array(
            //'type' => 'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'comment' => app::get('systrade')->_('捆绑的子订单号'),
        ),
        'logistics_company' => array(
            //'type' => 'varchar(30)',
            'type' => 'string',
            'length' => 30,
            'comment' => app::get('systrade')->_('子订单发货的快递公司'),
        ),
        'invoice_no' => array(
            //'type' => 'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'comment' => app::get('systrade')->_('子订单所在包裹的运单号'),
        ),
        'divide_order_fee' => array(
            'type' => 'money',
            'default' => '0',
            'comment' => app::get('systrade')->_('分摊之后的实付金额'),
        ),
        'part_mjz_discount' => array(
            'type' => 'money',
            'default' => '0',
            'comment' => app::get('systrade')->_('优惠分摊'),
        ),
        'total_fee' => array(
            'type' => 'money',
            'default' => '0',
            'comment' => app::get('systrade')->_('应付金额'),
        ),
        'payment' => array(
            'type' => 'money',
            'default' => '0',
            'comment' => app::get('systrade')->_('实付金额'),
        ),
        'discount_fee' => array(
            'type' => 'money',
            'default' => '0',
            'comment' => app::get('systrade')->_('子订单级订单优惠金额'),
        ),
        'adjust_fee' => array(
            'type' => 'money',
            'default' => '0',
            'comment' => app::get('systrade')->_('手工调整金额'),
        ),
        'modified_time' => array(
            'type' => 'last_modify',
            'comment' => app::get('systrade')->_('最后更新时间'),
        ),
        'status' => array(
            'type' => array(
                'WAIT_BUYER_PAY' => '等待买家付款',
                'WAIT_SELLER_SEND_GOODS' => '等待卖家发货,即:买家已付款',
                'WAIT_BUYER_CONFIRM_GOODS' => '等待买家确认收货,即:卖家已发货',
                'TRADE_BUYER_SIGNED' => '买家已签收,货到付款专用',
                'TRADE_FINISHED' => '交易成功',
                'TRADE_CLOSED_AFTER_PAY' => '付款以后,用户退款成功，交易自动关闭',
                'TRADE_CLOSED_BEFORE_PAY' => '付款以前,卖家或买家主动关闭交易',
            ),
            'default' => 'WAIT_BUYER_PAY',
            'required' => true,
            'comment' => app::get('systrade')->_('子订单状态'),
        ),
        'aftersales_status' => array(
            'type' => array(
                'WAIT_SELLER_AGREE' => '买家已经申请退款，等待卖家同意',
                'WAIT_BUYER_RETURN_GOODS' => '卖家已经同意退款，等待买家退货',
                'WAIT_SELLER_CONFIRM_GOODS' => '买家已经退货，等待卖家确认收货',
                'SUCCESS' => '退款成功',
                'CLOSED' => '退款关闭',
                'REFUNDING' => '退款中',
                'SELLER_REFUSE_BUYER' => '卖家拒绝退款',
                'SELLER_SEND_GOODS' => '卖家已发货',
            ),
            'required' => false,
            'comment' => app::get('systrade')->_('售后状态'),
        ),
        'complaints_status' => array(
            'type' => array(
                'NOT_COMPLAINTS' => '买家未进行投诉',
                'WAIT_SYS_AGREE' => '买家投诉，等待平台处理',
                'FINISHED' => '处理完成',
                'BUYER_CLOSED' => '买家撤销投诉',
                'CLOSED' => '平台关闭投诉，不需要处理直接关闭',
            ),
            'default' => 'NOT_COMPLAINTS',
            'comment' => app::get('systrade')->_('订单投诉状态'),
        ),
        'refund_fee' => array(
            'type' => 'money',
            'default' =>'0',
            'comment' => app::get('systrade')->_('退款金额'),
        ),
        'buyer_rate' => array(
            'type' => 'bool',
            /*
            'type' => array(
                'true' => '已评价',
                'false' => '未评价',
            ),
            */
            'default' => 0,
            'comment' => app::get('systrade')->_('买家是否已评价'),
        ),
        'anony' => array(
            'type' => 'bool',
            'default' => 0,
            'comment' => app::get('systrade')->_('是否匿名'),
        ),
        'seller_rate' => array(
            'type' => 'bool',
            /*
            'type' => array(
                'true' => '已评价',
                'false' => '未评价',
            ),
            */
            'default' => 0,
            'comment' => app::get('systrade')->_('卖家是否已评价'),
        ),
        'cat_service_rate' => array(
          'type' => 'money',
          'default' => '0',
          'label' => app::get('systrade')->_('商家三级类目签约佣金比例'),
          'width' => 75,
          'in_list' => true,
        ),
        'order_from' => array(
            //'type' => 'varchar(45)',
            'type' => 'string',
            'length' => 45,
            'comment' => app::get('systrade')->_('订单来源'),
        ),
        'pic_path' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'comment' => app::get('systrade')->_('商品图片绝对路径'),
        ),
        'timeout_action_time' => array(
            'type' => 'time',
            'comment' => app::get('systrade')->_('订单超时到期时间'),
        ),
        'outer_iid' => array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'comment' => app::get('systrade')->_('商家外部编码'),
        ),
        'outer_sku_id' => array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'comment' => app::get('systrade')->_('商家外部sku码'),
        ),
        'sub_stock' => array(
            'type' => 'bool',
            'required' => true,
            'default' => 1, //默认下单减库存
            'label' => app::get('systrade')->_('是否支持下单减库存'),
            'comment' => app::get('systrade')->_('是否支持下单减库存'),
            'in_list' => true,
            'default_in_list' => false,
        ),
    ),
    'primary' => 'oid',
    'comment' => app::get('systrade')->_('订单子表'),
);
