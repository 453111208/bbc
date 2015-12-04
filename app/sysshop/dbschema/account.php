<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
return  array(
    'columns'=>
    array(
        'seller_id'=>
        array(
            'type'=>'number',
            //'pkey'=>true,
            'autoincrement' => true,
            'comment' => app::get('sysshop')->_('商家账户序号ID'),
        ),
        'login_account'=>
        array(
            //'type'=>'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'is_title'=>true,
            'required' => true,
            'comment' => app::get('sysshop')->_('登录名'),
        ),
        'login_password'=>
        array(
            //'type'=>'varchar(60)',
            'type' => 'string',
            'length' => 60,
            'required' => true,
            'comment' => app::get('sysshop')->_('登录密码'),
        ),
        'disabled'=>
        array(
            'type'=>'bool',
            'default'=>0,
        ),
        'createtime'=>
        array(
            'type'=>'time',
            'comment' => app::get('sysshop')->_('创建时间'),
        ),
        'modified_time' =>
        array (
            'type' => 'last_modify',
            'label' => app::get('sysshop')->_('最后修改时间'),
        ),
    ),
    
    'primary' => 'seller_id',
    'comment' => app::get('sysshop')->_('商家会员表'),
);
