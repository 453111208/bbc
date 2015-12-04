<?php
// 保证金扣除记录
return array (
    'columns' =>
    array (
        'reco_id' =>
        array (
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('保证金扣除记录'),
        ),

        'item_id'=>array(
            'type' => 'string',
            'default' => '0',
            'label' => app::get('sysshoppubt')->_('交易id'),
            'comment' => app::get('sysshoppubt')->_('交易id'),
        ),

        'shop_id' => array(
            'type' => 'table:shop@sysshop',
            'required' => true,
            'comment' => app::get('sysshoppubt')->_('所属企业'),
             'label' => app::get('sysshoppubt')->_('所属企业'),
        ),

        'shop_name'=>array(
            //'type'=>'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'in_list'=>true,
            'width' => 110,
            'default_in_list'=>true,
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => true,
            'filterdefault' => 'true',
            'label' => app::get('sysshoppubt')->_('企业名称'),
             'label' => app::get('sysshoppubt')->_('企业名称'),
            'order' => 13,
        ),

        'user_id' => array(
            'type' => 'string',
            'default' => '0',
            //'pkey' => true,
            'comment' => app::get('sysshoppubt')->_('投标用户id'),
        ),

        'money'=>array(
            'type' => 'money',
            'default' => 0,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'label' => app::get('sysshoppubt')->_('金额'),
            'comment' => app::get('sysshoppubt')->_('金额'),
            'order' => 8,
        ),

        'type'=>array(
            'type' => array(
                '0' => app::get('sysshoppubt')->_('招标'),
                '1' => app::get('sysshoppubt')->_('竞价'),
            ),
            'default' => '0',
            'width' => 110,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'label' => app::get('sysshoppubt')->_('交易类型'),
            'comment' => app::get('sysshoppubt')->_('交易类型'),
            'order' => 8,
        ),
        'create_time' => array(
            'type' => 'time',
            'label' => app::get('sysshoppubt')->_('提交时间'),
            'comment' => app::get('sysshoppubt')->_('提交时间'),
            'in_list' => true,
            'default_in_list' => false,
            'order'=>5,
        ),
    ),
    'primary' => 'reco_id',
    'comment' => app::get('sysshoppubt')->_('保证金扣除记录'),
);
