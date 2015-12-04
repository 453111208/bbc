<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array(
    'columns' => array(
        'paybill_id' => array(
            'type' => 'number',
            'required' => true,
            'autoincrement'=> true,
            'comment' => app::get('ectools')->_('子支付单编号'),
        ),
        'payment_id' => array(
            'type' => 'string',
            'required' => true,
            'length' => 20,
            'comment' => app::get('ectools')->_('主支付单编号'),
        ),
        'tid' => array(
            'type' => 'string',
            'required' => true,
            'length' => 20,
            'comment' => app::get('ectools')->_('被支付订单编号'),
        ),
        'status' => array(
            'type' => array (
                'succ' => app::get('ectools')->_('支付成功'),
                'failed' => app::get('ectools')->_('支付失败'),
                'cancel' => app::get('ectools')->_('未支付'),
                'error' => app::get('ectools')->_('处理异常'),
                'invalid' => app::get('ectools')->_('非法参数'),
                'progress' => app::get('ectools')->_('已付款至担保方'),
                'timeout' => app::get('ectools')->_('超时'),
                'ready' => app::get('ectools')->_('准备中'),
            ),
            'required' => true,
            'default' => 'ready',
            'length' => 20,
            'comment' => app::get('ectools')->_('该订单支付的状态'),
        ),
        'payment' => array(
            'type' => 'string',
            'required' => true,
            'length' => 20,
            'comment' => app::get('ectools')->_('该订单支付的金额'),
        ),
        'user_id' => array (
            'type' => 'string',
            'length' => 100,
            'label' => app::get('ectools')->_('会员'),
            'comment' => app::get('ectools')->_('会员id'),
        ),
        'payed_time' => array (
            'type' => 'time',
            'label' => app::get('ectools')->_('支付完成时间'),
            'comment' => app::get('ectools')->_('支付完成时间'),
        ),
        'created_time' => array (
            'type' => 'time',
            'label' => app::get('ectools')->_('支付开始时间'),
            'comment' => app::get('ectools')->_('支付单创建时间'),
        ),
        'modified_time' => array (
            'type' => 'time',
            'label' => app::get('ectools')->_('最后修改时间'),
            'comment' => app::get('ectools')->_('最后更新时间'),
        ),
   ),
   'primary' => 'paybill_id',
    'comment' => app::get('ectools')->_('订单支付单据记录'),
);


