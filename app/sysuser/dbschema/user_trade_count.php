<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array (
    'columns' => array(
        'user_id' => array(
            'type' =>'table:user',
            'comment' => app::get('sysuser')->_('会员id'),
        ),
        'unpay' => array(
            'type' => 'number',
            'comment' => app::get('sysuser')->_('未支付订单数量'),
        ),
        'undelivery' => array(
            'type' => 'number',
            'comment' => app::get('sysuser')->_('未发货订单数量'),
        ),
        'unreceived' => array(
            'type' => 'number',
            'comment' => app::get('sysuser')->_('未确认收货订单数量'),
        ),
        'unrate' => array(
            'type' => 'number',
            'comment' => app::get('sysuser')->_('未评论订单'),
        ),
    ),
);
