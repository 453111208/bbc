<?php

/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array(
    'columns' => array(
        'id' => array(
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
        ),
        'activity_id' => array(
            'type' => 'number',
            'comment' => app::get('syspromotion')->_('活动id'),
        ),
        'shop_id' => array(
            'type' => 'number',
            'required' => true,
            'comment' => app::get('sysshop')->_('商家店铺id'),
        ),
        'item_id' => array(
            'type' => 'number',
            'comment' => app::get('syspromotion')->_('商品id'),
        ),
        'cat_id' => array(
            'type' => 'number',
            'comment' => app::get('syspromotion')->_('商品关联的三级类目id'),
        ),
        'title' => array(
            'type' => 'string',
            'length' => 90,
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('syspromotion')->_('商品名称'),
            'comment' => app::get('syspromotion')->_('商品名称'),
        ),
        'item_default_image' => array(
            'type' => 'string',
            'comment' => app::get('syspromotion')->_('商品原默认图'),
        ),
        // 'ad_image' => array(
        //     'type' => 'string',
        //     'comment' => app::get('syspromotion')->_('活动广告图'),
        // ),
        'price' => array(
            'type' => 'money',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('syspromotion')->_('商品原价'),
            'comment' => app::get('syspromotion')->_('商品原价'),
        ),
        'activity_price' => array(
            'type' => 'money',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('syspromotion')->_('促销价格'),
            'comment' => app::get('syspromotion')->_('促销价格'),
        ),
        'sales_count' => array(
            'type' => 'number',
            'default' => 0,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('syspromotion')->_('本活动销量'),
            'comment' => app::get('syspromotion')->_('本活动销量'),
        ),
        'verify_status' => array(
            'type' => array(
                'pending' => '待审核',
                'refuse' => '审核被拒绝',
                'agree' => '审核通过',
                // 'again' => '再次申请',
            ),
            'default' => 'pending',
            'required'=> true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('syspromotion')->_('审核状态'),
            'comment' => app::get('syspromotion')->_('审核状态'),
        ),
        'start_time' => array(
            'type' => 'time',
            'default' => 0,
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'filterdefault' => true,
            'label' => app::get('syspromotion')->_('活动生效开始时间'),
            'comment' => app::get('syspromotion')->_('活动生效开始时间'),
        ),
        'end_time' => array(
            'type' =>'time',
            'default' => 0,
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'filterdefault' => true,
            'label' => app::get('syspromotion')->_('活动生效结束时间'),
            'comment' => app::get('syspromotion')->_('活动生效结束时间'),
        ),
        'activity_tag' => array(
            'type' => 'string',
            'lenght' => '15',
            'required' => true,
            'default_in_list' => true,
            'in_list' => true,
            'searchtype' => 'has',
            'filtertype' => 'custom',
            'filterdefault' => true,
            'label' => app::get('syspromotion')->_('活动标签'),
            'comment' => app::get('syspromotion')->_('活动标签'),
        ),
    ),
    'primary' => 'id',
    'index' => array(
        'ind_activitywithitem' => [
            'columns' => ['activity_id', 'item_id'],
            'prefix' => 'unique',
        ],
    ),
    'comment' => app::get('syspromotion')->_('活动商品表'),
);
