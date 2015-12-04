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
        'user_id' =>
        array (
            'type' => 'table:account@sysuser',
            //'pkey' => true,
            'label' => app::get('sysuser')->_('会员用户名'),
        ),
        'grade_id' =>
        array (
            'type' => 'table:user_grade',
            'required' => true,
            'default' => 1,
            'label' => app::get('sysuser')->_('会员等级'),
            'order' => 40,
        ),
        'name' =>
        array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'label' => app::get('sysuser')->_('昵称'),
            'width' => 75,
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'in_list' => true,
            'is_title'=>true,
            'default_in_list' => false,
        ),
        'username' =>
        array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'label' => app::get('sysuser')->_('真实姓名'),
            'width' => 75,
            'searchtype' => 'has', // 简单搜索
            'filtertype' => 'normal', // 高级搜索
            'filterdefault' => 'true',
            'in_list' => true,
            'is_title' => true,
            'default_in_list' => false,
        ),
        'point' =>
        array (
            //'type' => 'int(10)',
            'type' => 'number',
            'default' => 0,
            'required' => true,
            'label' => app::get('sysuser')->_('积分'),
            'width' => 110,
        ),
        'refer_id' =>
        array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'label' => app::get('sysuser')->_('来源ID'),
            'width' => 75,
            'editable' => false,
            'filtertype' => 'normal',
            'in_list' => false,
        ),
        'refer_url' =>
        array (
            //'type' => 'varchar(200)',
            'type' => 'string',
            'length' => 200,
            'label' => app::get('sysuser')->_('推广来源URL'),
            'width' => 75,
            'editable' => false,
            'filtertype' => 'normal',
            'in_list' => false,
        ),
        'birthday' =>
        array (
            'label' => app::get('sysuser')->_('生日'),
            'type' => 'time',
            'width' => 100,
            'editable' => false,
            'in_list'=>true,
            'default_in_list' => true,
        ),
        'sex' =>
        array (
            'type' =>
            array (
                0 => app::get('sysuser')->_('女'),
                1 => app::get('sysuser')->_('男'),
                2 => '-',
            ),
            'default' => 2,
            'required' => true,
            'label' => app::get('sysuser')->_('性别'),
            'order' => 30,
            'width' => 40,
            'editable' => true,
            'filtertype' => 'yes',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'wedlock' =>
        array (
            'type' => 'bool',
            'default' => '0',
            'required' => true,
            'editable' => false,
            'comment' => app::get('sysuser')->_('婚姻状况'),
        ),
        'education' =>
        array (
            //'type' => 'varchar(30)',
            'type' => 'string',
            'length' => 30,
            'editable' => false,
            'comment' => app::get('sysuser')->_('教育程度'),
        ),
        'vocation' =>
        array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'editable' => false,
            'comment' => app::get('sysuser')->_('职业'),
        ),
        'reg_ip' =>
        array (
            //'type' => 'varchar(16)',
            'type' => 'string',
            'length' => 16,
            'label' => app::get('sysuser')->_('注册IP'),
            'width' => 110,
            'editable' => false,
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysuser')->_('注册时IP地址'),
        ),
        'regtime' =>
        array (
            'label' => app::get('sysuser')->_('注册时间'),
            'width' => 150,
            'type' => 'time',
            'editable' => false,
            'filtertype' => 'time',
            'filterdefault' => true,
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysuser')->_('注册时间'),
        ),
        'cur' =>
        array (
            //'type' => 'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'label' => app::get('sysuser')->_('货币'),
            'width' => 110,
            'editable' => false,
            'comment' => app::get('sysuser')->_('货币(偏爱货币)'),
        ),
        'lang' =>
        array (
            //'type' => 'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'label' => app::get('sysuser')->_('语言'),
            'width' => 110,
            'editable' => false,
            'comment' => app::get('sysuser')->_('偏好语言'),
        ),
        'disabled' =>
        array (
            'type' => 'bool',
            'default' => 0,
            'editable' => false,
        ),
        'experience' =>
        array (
            //'type' => 'int(10)',
            'type' => 'number',
            'label' => app::get('sysuser')->_('经验值'),

            'default' => 0,
        ),
        'source' =>
        array (
            'type' => array(
                'pc' =>app::get('sysuser')->_('标准平台'),
                'wap' => app::get('sysuser')->_('手机触屏'),
                'weixin' => app::get('sysuser')->_('微信商城'),
                'api' => app::get('sysuser')->_('API注册')
            ),
            'required' => false,
            'label' => app::get('sysuser')->_('平台来源'),
            'width' => 110,
            'editable' => false,
            'default' =>'pc',
            'in_list' => true,
            'default_in_list' => false,
            'filterdefault' => false,
            'filtertype' => 'normal',
        ),

       'hjadvance' =>
        array (
          'type' => 'money',
          'default' => '0.00',
          'label' => app::get('sysuser')->_('预存款'),
          'comment' => app::get('sysuser')->_('会员账户余额'),
        ),
        
        'hjadvance_freeze' =>
        array (
          'type' => 'money',
          'default' => '0.00',
          'comment' => app::get('sysuser')->_('会员预存款冻结金额'),
        ),

        'area' =>
        array (
            'label' => app::get('sysuser')->_('地区'),
            'width' => 110,
            //'type' => 'varchar(55)',
            'type' => 'string',
            'length' => 55,
            'editable' => false,
            'filtertype' => 'yes',
            'filterdefault' => 'true',
        ),
        'addr' =>
        array (
            'type' => 'varchar(255)',
            'type' => 'string',
            'label' => app::get('sysuser')->_('地址'),
            'width' => 110,
            'editable' => true,
            'filtertype' => 'normal',
            'in_list' => true,
            'default_in_list' => false,
        ),

        'email_verify' => array (
            'type' => 'bool',
            'label' => app::get('sysuser')->_('是否通过邮箱验证'),
            'default' => 0,
        ),
    ),

    'primary' => 'user_id',
    'index' => array(
        'ind_regtime' => ['columns' => ['regtime']],
        'ind_disabled' => ['columns' => ['disabled']],
    ),

    'comment' => app::get('sysuser')->_('商店会员表'),
);
