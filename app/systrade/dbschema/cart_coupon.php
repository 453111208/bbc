<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return  array(
    'columns' => array(
        'id' => array(
            'type' => 'number',
            'autoincrement' => true,
            'required' => true,
        ),
        'user_id' => array(
            'type' => 'integer',
            'required' => true,
            'comment' => app::get('systrade')->_('会员id'),
            'label' => app::get('systrade')->_('会员id'),
        ),
        'shop_id'=> array(
            'type'=>'number',
            'required' => true,
            'comment' => app::get('systrade')->_('店铺ID'),
            'label' => app::get('systrade')->_('店铺ID'),
        ),
        'coupon_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => app::get('systrade')->_('优惠券id'),
            'comment' => app::get('systrade')->_('优惠券id'),
        ),
        'coupon_code' => array(
            'type' => 'string',
            'length' => 15,
            'default' => '',
            'required' => true,
            'comment' => app::get('systrade')->_('优惠券号码'),
            'label' => app::get('systrade')->_('优惠券号码'),
        ),
    ),
    'primary' => 'id',
    'index' => array(
        'ind_shopusers_id' => ['columns' => ['shop_id','user_id'], 'prefix'=>'unique' ],
        // 'ind_user_id' => ['columns' => 'user_id'],
        // 'ind_shop_id' => ['columns' => 'shop_id'],
    ),
    'unbackup' => true,
    'comment' => app::get('systrade')->_('购物车使用优惠券表'),
);
