<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return  array(
    'columns'=>array(
        'account_id'=>array(
            'type'=>'number',
            //'pkey'=>true,
            'autoincrement' => true,
            'comment' => app::get('desktop')->_('账户序号ID'),
        ),
        'account_type'=>array(
            //'type'=>'varchar(30)',
            'type' => 'string',
            'length' => 30,

            'comment' => app::get('desktop')->_('账户类型(会员和管理员等)'),
        ),
        'login_name'=>array(
            //'type'=>'varchar(100)',
            'type' => 'string',
            'length' => 100,

            'is_title'=>true,
            'required' => true,
            'comment' => app::get('desktop')->_('登录用户名'),
        ),
        'login_password'=>array(
            //'type'=>'varchar(60)',
            'type' => 'string',
            'length' => 60,

            'required' => true,
            'comment' => app::get('desktop')->_('登录密码'),
        ),
        'disabled'=>array(
            'type'=>'bool',
            'default'=>0,
        ),
        'createtime'=>array(
            'type'=>'time',
            'comment' => app::get('desktop')->_('创建时间'),
        ),
    ),
    'primary' => 'account_id',
    'index' => array(
        'ind_account' => [
            'columns' => ['login_name', 'disabled'],
            'prefix' => 'unique',
        ],
    ),
    'comment' => app::get('desktop')->_('用户权限账户表'),
);

