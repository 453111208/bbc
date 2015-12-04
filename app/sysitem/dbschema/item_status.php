<?php
return  array(
    'columns'=> array(
        'item_id' => array(
            'type' => 'table:item',
            'required' => true,
            //'pkey' => true,
            'comment' => app::get('sysitem')->_('商品 ID'),
        ),
        'shop_id' => array(
            'type' => 'table:shop@sysshop',
            'required' => true,
            'label' => app::get('sysitem')->_('所属店铺'),
            'comment' => app::get('sysitem')->_('店铺id'),
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
            'filtertype' => 'yes',
            'filterdefault' => true,
            'order' => 11,
        ),
        'approve_status' => array(
            'type' => array(
                'onsale' => app::get('sysitem')->_('出售中'),
                'instock' => app::get('sysitem')->_('库中'),
            ),
            'required' => true,
            'default' => 'instock',
            'label' => app::get('sysitem')->_('商品状态'),
            'comment' => app::get('sysitem')->_('商品状态'),
            'in_list' => true,
            'default_in_list' => false,
            'filtertype' => 'yes',
            'filterdefault' => true,
            'order'=>19,
        ),
        'list_time' => array(
            'type' => 'time',
            'label' => app::get('sysitem')->_('上架时间'),
            'comment' => app::get('sysitem')->_('上架时间'),
            'in_list' => true,
            'default_in_list' => false,
            'order'=>20,
        ),
        'delist_time' => array(
            'type' => 'time',
            'label' => app::get('sysitem')->_('下架时间'),
            'comment' => app::get('sysitem')->_('下架时间'),
            'in_list' => true,
            'default_in_list' => false,
            'order'=>21,
        ),
    ),
    
    'primary' => 'item_id',
    'comment' => app::get('sysitem')->_('商品上下架状态表'),
);
