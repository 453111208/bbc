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
        'snotify_id' => array (
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'label' => 'ID',
            'width' => 110,
            'editable' => false,
            'default_in_list' => true,
            'id_title' => true,
        ),
        'shop_id' => array (
            'type' => 'table:shop@sysshop',
            'required' => true,
            'label' => app::get('sysuser')->_('商店名称名称'),
            'in_list' => true,
            'comment' => app::get('sysuser')->_('商店ID'),
        ),
        'user_id' => array(
            'type'=>'table:user',
            'in_list' => true,
            'label' => app::get('sysuser')->_('会员用户名'),
            'default_in_list' => true,
        ),
        'shop_name'=>array(
            //'type'=>'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'required'=>true,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => false,
            'filterdefault' => 'true',
            'label' => app::get('sysuser')->_('店铺名称'),
            'comment' => app::get('sysuser')->_('店铺名称'),
            'order' => 5,
        ),
        'shop_logo'=>array(
            //'type'=>'varchar(500)',
            'type' => 'string',
            'length' => 500,
            'label' => app::get('sysuser')->_('店铺logo'),
            'comment' => app::get('sysuser')->_('提交logo'),
            'in_list'=>true,
            'default_in_list'=>false,
            'order' => 10,
        ),

        'create_time' =>
        array (
            'type' => 'time',
            'label' => app::get('sysuser')->_('申请时间'),
            'width' => 110,
            'editable' => false,
            'filtertype' => 'time',
            'filterdefault' => true,
            'in_list' => true,
        )
    ),
    
    'primary' => 'snotify_id',
);
