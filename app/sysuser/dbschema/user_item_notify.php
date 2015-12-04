<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
return array(
    'columns'=>
    array(
        'gnotify_id'=>
        array(
            'type'=>'number',
            'autoincrement' => true,
            'comment' => app::get('sysuser')->_('缺货ID'),
        ),
        'shop_id' => array(
            'type' => 'number',
            'required' => true,
            'comment' => app::get('sysuser')->_('店铺id'),
            'order' => 2,
        ),
        'item_id'=>
        array(
            'type' => 'number',
            'required' => true,
            'length' => 100,
            'comment' => app::get('sysuser')->_('商品id'),
            'order' => 3,
        ),
        'sku_id'=>
        array(
            'type' => 'number',
            'required' => true,
            'length' => 100,
            'comment' => app::get('sysuser')->_('货品id'),
            'order' => 4,
        ),
        'email'=>
        array(
            'type' => 'string',
            'length' => 32,
            'comment' => app::get('sysuser')->_('邮箱'),
            'order' => 5,
        ),
        'createtime'=>
        array(
            'type'=>'time',
            'comment' => app::get('sysuser')->_('创建时间'),
            'order' => 9,
        ),
        'send_time' =>
        array (
            'type' => 'time',
            'label' => app::get('sysuser')->_('最后发送时间'),
            'order' => 10,
        ),
        'sendstatus' =>
        array (
            'type' => array(
                'ready' => app::get('sysuser')->_('准备发送'),
                'send' => app::get('sysuser')->_('以发送'),
            ),
            'default' => 'ready',
            'label' => app::get('sysuser')->_('发送状态'),
            'comment' => app::get('sysuser')->_('状态'),
            'order' => 11,
        ),
        

    ),
    'primary' => 'gnotify_id',
    'comment' => app::get('sysuser')->_('缺货表'),
);

