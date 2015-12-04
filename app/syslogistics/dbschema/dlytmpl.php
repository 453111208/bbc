<?php

return  array(
    'columns' => array(
        'template_id' => array(
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'label' => app::get('syslogistics')->_('模块ID'),
            'width' => 110,
        ),
        'shop_id' => array(
            'type'=>'table:shop@sysshop',
            'label' => app::get('syslogistics')->_('店铺名称'),
            'required' => true,
            'in_list' => true,
            'default_in_list'=>true,
        ),
        'name' =>
        array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'label' => app::get('syslogistics')->_('模板名称'),
            'width' => 180,
            'editable' => true,
            'in_list' => true,
            'is_title' => true,
            'default_in_list' => true,
        ),
        'valuation' =>
        array (
            'type' =>
            array (
                '0' => app::get('syslogistics')->_('按件数'),
                '1' => app::get('syslogistics')->_('按重量'),
                '2' => app::get('syslogistics')->_('按体积'),
            ),
            'default' => '1',
            'comment' => app::get('syslogistics')->_('运费计算参数来源'),
        ),
        'corp_id' =>
        array (
            'type' => 'number',
            'editable' => false,
            'required' => false,
            'comment' => app::get('syslogistics')->_('物流公司ID'),
        ),
        'order_sort' =>
        array (
            'type' => 'number',
            'label' => app::get('syslogistics')->_('排序'),
            'width' => 150,
            'default' => 0,
            'in_list' => true,
            'default_in_list' => false,
        ),
        'protect' =>
        array (
            'type' => 'bool',
            'default' => 0,
            'required' => true,
            'label' => app::get('syslogistics')->_('物流保价'),
            'width' => 75,
        ),
        'protect_rate' =>
        array (
            //'type' => 'float(6,3)',
            'type' => 'decimal',
            'precision' => 6,
            'scale' => 3,
            
            'editable' => false,
            'comment' => app::get('syslogistics')->_('保价费率'),
        ),
        'minprice' =>
        array (
            //'type' => 'float(10,2)',
            'type' => 'decimal',
            'precision' => 10,
            'scale' => 2,
            
            'default' => '0.00',
            'required' => true,
            'editable' => false,
            'comment' => app::get('syslogistics')->_('保价费最低值'),
        ),
        'status' =>
        array (
            'type' =>
            array (
                'off' => app::get('syslogistics')->_('关闭'),
                'on' => app::get('syslogistics')->_('启用'),
            ),
            'default' => 'on',
            'comment' => app::get('syslogistics')->_('是否开启'),
        ),
        'fee_conf' =>
        array(
            'type' => 'text',
            'required' => false,
            'default' => '',
            'editable' => false,
            'comment' => app::get('syslogistics')->_('运费模板中运费信息对象，包含默认运费和指定地区运费'),
        ),
        'create_time'=>
        array(
            'type'=>'time',
            'comment' => app::get('syslogistics')->_('创建时间'),
        ),
        'modifie_time'=>
        array(
            'type'=>'last_modify',
            'comment' => app::get('syslogistics')->_('最后修改时间'),
        ),
    ),
    'primary' => 'template_id',
    'index' => array(
        'ind_shop_temp_id' => ['columns' => ['shop_id', 'template_id']],
        'ind_shop_id' => ['columns' => ['shop_id']],
    ),
    'comment' => app::get('syslogistics')->_('快递模板配置表'),
);

