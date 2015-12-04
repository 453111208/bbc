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
        'user_id'=>
        array(
            'type'=>'number',
            //'pkey'=>true,
            'autoincrement' => true,
            'comment' => app::get('sysuser')->_('用户ID'),
        ),
        'login_account'=>
        array(
            //'type'=>'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'is_title'=>true,
            'comment' => app::get('sysuser')->_('用户名'),
        ),
        'email'=>
        array(
            //'type'=>'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'comment' => app::get('sysuser')->_('邮箱'),
        ),
        'mobile'=>
        array(
            //'type'=>'varchar(32)',
            'type' => 'string',
            'length' => 32,
            'comment' => app::get('sysuser')->_('手机号'),
        ),
        'login_password'=>
        array(
            //'type'=>'varchar(60)',
            'type' => 'string',
            'length' => 60,
            'required' => true,
            'comment' => app::get('sysuser')->_('登录密码'),
        ),
        'login_type'=>
        array(
            //'type'=>'varchar(60)',
            'type' => 'string',
            'length' => 60,
            'default' => 'common',//common普通登录 trustlogin 信任登录
            'required' => true,
            'comment' => app::get('sysuser')->_('登录类型，信任登录或者普通登录'),
        ),
        'disabled'=>
        array(
            'type'=>'bool',
            'default'=>0,
        ),
        'createtime'=>
        array(
            'type'=>'time',
            'comment' => app::get('sysuser')->_('创建时间'),
        ),
        'modified_time' =>
        array (
            'type' => 'last_modify',
            'label' => app::get('sysuser')->_('最后修改时间'),
        ),
    ),
    'primary' => 'user_id',
    'comment' => app::get('sysuser')->_('商城会员用户表'),
);

