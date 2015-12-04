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
        'reco_id' =>
        array (
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('sysuser')->_('存款记录ID'),
        ),

        
        'user_id' =>
        array (
            'type' => 'number',
            'comment' => app::get('sysuser')->_('用户ID'),
        ),

        'changemoney' =>
        array (
          'type' => 'money',
          'default' => '0.00',
          'comment' => app::get('sysuser')->_('存款的改变数目'),
        ),

        'text' => 
        array (
            'type' => 'text',
            'comment' => app::get('sysuser')->_('意见描述'),
        ),

        'name' =>
        array(
            'type' => 'string',
            'length' => 80,
            'comment' => app::get('sysuser')->_('操作人'),
        ),

        'username' =>
        array(
            'type' => 'string',
            'length' => 80,
            'comment' => app::get('sysuser')->_('户名'),
        ),


        'cardnum' =>
        array(
            'type' => 'string',
            'length' => 80,
            'comment' => app::get('sysuser')->_('账号'),
        ),

        'bank' =>
        array(
            'type' => 'string',
            'length' => 150,
            'comment' => app::get('sysuser')->_('开户行'),
        ),

        'likeman' =>
        array(
            'type' => 'string',
            'length' => 80,
            'comment' => app::get('sysuser')->_('联系人'),
        ),

        'phone' =>
        array(
            'type' => 'string',
            'length' => 80,
            'comment' => app::get('sysuser')->_('联系电话'),
        ),

        'payway' =>
        array (
            'type' =>
            array (
                0 => app::get('sysuser')->_('汇票'),
                1 => app::get('sysuser')->_('微信'),
                2 => app::get('sysuser')->_('银行卡'),
                3 => app::get('sysuser')->_('支付宝'),
                4 => app::get('sysuser')->_('其他'),
            ),
            'default' => '2',
            'comment' => app::get('sysuser')->_('支付方式'),
            'width' => 40,
        ),

        'paytime' => 
        array(
            'type' => 'time',
            'comment' => app::get('sysuser')->_('支付日期'),
        ),

        'explain' =>
        array(
            'type' => 'string',
            'length' => 500,
            'comment' => app::get('sysuser')->_('说明'),
        ),

        'ensurence' =>
        array(
            'type' => 'string',
            'length' => 100,
            'comment' => app::get('sysuser')->_('凭证'),
        ),

        'pay' =>
        array (
            'type' =>
            array (
                '0' => app::get('sysuser')->_('充值'),
                '1' => app::get('sysuser')->_('消费'),
                '2' => app::get('sysuser')->_('保证金提现'),
            ),
            'default' => '2',
            'comment' => app::get('sysuser')->_('类型'),
            'width' => 40,
        ),

        'types' =>
        array(
            'type' =>
            array (
                '0' => app::get('sysuser')->_('竞价'),
                '1' => app::get('sysuser')->_('招标'),
                '2' => app::get('sysuser')->_('其他'),
            ),
            'default' => '2',
            'comment' => app::get('sysuser')->_('类型'),
            'width' => 40,
        ),

        'bidding_id' =>
        array (
            'type' => 'string',
            'length' => 100,
            'comment' => app::get('sysuser')->_('竞价id'),
        ),

        'tender_id' =>
        array (
            'type' => 'string',
            'length' => 100,
            'comment' => app::get('sysuser')->_('招标id'),
        ),

        'create_time' => 
        array(
            'type' => 'time',
            'comment' => app::get('sysuser')->_('创建时间'),
        ),

    ),

    'primary' => 'reco_id',
    'comment' => app::get('sysuser')->_('充钱使用记录'),
);
