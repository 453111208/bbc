<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return  array(
    'columns' => array(
        'rate_id' => array(
            //'type' => 'bigint unsigned',
            'type' => 'bigint',
            'unsigned' => true,
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'comment' => app::get('sysrate')->_('评价ID'),
        ),
        'tid' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'label' => app::get('sysrate')->_('订单号'),
            'comment' => app::get('sysrate')->_('订单号'),
            'editable' => false,
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'filterdefault' => true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'oid' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'comment' => app::get('sysrate')->_('子订单号'),
        ),
        'user_id' =>
        array(
            'type' => 'table:account@sysuser',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysrate')->_('会员'),
        ),
        'shop_id' =>
        array(
            'type' => 'table:shop@sysshop',
            'label' => app::get('sysrate')->_('所属商家'),
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysrate')->_('店铺ID'),
        ),
        /*-----------商品信息冗余------------------*/
        'item_id' => array(
            'type' => 'number',
            'required' => true,
            'comment' => app::get('sysrate')->_('评论的商品ID'),
        ),
        'item_title' => array(
            //'type' => 'varchar(60)',
            'type' => 'string',
            'length' => 60,
            'required' => true,
            'default' => '',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysrate')->_('商品标题'),
        ),
        'item_price' => array(
            'type' => 'money',
            'default' => '0',
            'comment' => app::get('sysrate')->_('商品价格'),
        ),
        'item_pic' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'length' => '1024',
            'comment' => app::get('sysrate')->_('商品图片绝对路径'),
        ),
        'spec_nature_info' => array (
            'type' => 'text',
            'comment' => app::get('sysrate')->_('sku描述'),
        ),
        /*-----------商品信息冗余------------------*/

        /*-----------评论信息-----------------*/
        'result' => array(
            'type' => ['good'=>'好评','neutral'=>'中评','bad'=>'差评'],
            'default' => 'good',
            'label' => app::get('sysrate')->_('评价结果'),
            'in_list' => true,
            'default_in_list' => false,
        ),
        'content' => array(
            'type' => 'text',
            'default' => '',
            'label' => app::get('sysrate')->_('评价内容'),
            'in_list' => true,
            'default_in_list' => false,
        ),
        'rate_pic' => array(
            //'type' => 'varchar(255)',
            'type' => 'text',
            'default' => '',
            'comment' => app::get('sysrate')->_('晒单图片'),
        ),
        'is_reply' => array(
            'type' => 'bool',
            'default' => '0',
            'label' => app::get('sysrate')->_('评价是否回复'),
            'in_list' => true,
            'default_in_list' => false,
        ),
        'reply_content' => array(
            'type' => 'text',
            'default' => '',
            'label' => app::get('sysrate')->_('评价回复'),
            'filtertype' => 'yes',
            'filterdefault' => true,
            'in_list' => true,
            'default_in_list' => false,
        ),
        'reply_time' => array(
            'type' => 'time',
            'label' => app::get('sysrate')->_('回复时间'),
            'width' => '100',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'anony' => array( //1 匿名 0 实名
            'type' => 'bool',
            'default' => '0',
            'required' => true,
            'comment' => app::get('sysrate')->_('是否匿名'),
        ),
        'role' => array(
            'type' => array(
                'seller' => '卖家',
                'buyer' => '买家',
            ),
            'required' => true,
            'default' => 'buyer',
            'label' => app::get('sysrate')->_('评价者角色'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'is_lock' => array(//1为锁定 0为不锁定
            'type' => 'bool',
            'default' => '1',
            'comment' => app::get('sysrate')->_('该评价是否被锁定'),
        ),
        /*-----------评论信息-----------------*/

        'is_appeal' => array(//1 为可以申诉，0为不可以申诉
            'type' => 'bool',
            'default' => '1',
            'comment' => app::get('sysrate')->_('是否可以申诉'),
        ),
        'appeal_status'=> array(
            'type' => ['NO_APPEAL'=>'未申诉','WAIT' => '等待批准','REJECT' => '申诉驳回','SUCCESS' => '申诉成功','CLOSE' => '申诉关闭'],
            'default' => 'NO_APPEAL',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysrate')->_('申诉状态'),
        ),
        'appeal_again' => array(
            'type' => 'bool',
            'default' => 0,
            'comment' => app::get('sysrate')->_('再次申诉'),
        ),
        'appeal_time' =>
        array(
            'type' => 'time',
            'label' => app::get('sysrate')->_('申诉时间'),
            'width' => '100',
            'in_list' => true,
            'default_in_list' => true,
        ),

        'created_time' =>
        array(
            'type' => 'time',
            'label' => app::get('sysrate')->_('创建时间'),
            'width' => '100',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'modified_time' => array(
            'type' => 'last_modify',
            'label' => app::get('sysrate')->_('最后修改时间'),
            'in_list' => true,
            'default_in_list' => true,
            'width' => '100',
        ),
        'disabled' => array(
            'type' => 'bool',
            'default' => 0,
            'required' => true,
            'editable' => false,
            'comment' => app::get('sysrate')->_('是否有效'),
        ),
    ),

    'primary' => 'rate_id',
    'comment' => app::get('sysrate')->_('商品评分表'),
);

