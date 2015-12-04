<?php
// 看样表
return array (
    'columns' =>
    array (
        'seegoods_id' =>
        array (
            //'type' => 'int(10)',
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('通知记录id'),
        ),

       'standard_id' =>
        array (
            'type' => 'string',
            'length' => 190,
            'comment' => app::get('sysshoppubt')->_('标准商品id'),
        ),

       'bidding_id' =>
        array (
            'type' => 'string',
            'length' => 190,
            'comment' => app::get('sysshoppubt')->_('竞价商品id'),
        ),

        'tender_id' =>
        array (
            'type' => 'string',
            'length' => 190,
            'comment' => app::get('sysshoppubt')->_('招标商品id'),
        ),

        'title' => array(
            //'type' => 'varchar(60)',
            'type' => 'string',
            'length' => 90,
            'width' => 210,
            'default' => '',
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => true,
            'filterdefault' => 'true',
            'comment' => app::get('sysshoppubt')->_('交易标题'),
             'label' => app::get('sysshoppubt')->_('交易标题'),
            'order' => 1,
        ),


        'type' => array(
            'type' => array(
                '0' => app::get('sysshoppubt')->_('标准'),
                '1' => app::get('sysshoppubt')->_('竞价'),
                '2' => app::get('sysshoppubt')->_('招标'),
            ),
            'default' => '0',
            'comment' => app::get('sysshoppubt')->_('类型'),
            'label' => app::get('sysshoppubt')->_('类型'),
            'width' => 110,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'order' => 2,
        ),

    'u_name' =>
        array (
            'is_title' => true,
            'type' => 'string',
            'length' => 50,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => true,
            'filterdefault' => 'true',
            'comment' => app::get('sysshoppubt')->_('联系人'),
             'label' => app::get('sysshoppubt')->_('联系人'),
            'order' => 3,
        ),

       'mobile' =>
        array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => true,
            'filterdefault' => 'true',
            'comment' => app::get('sysshoppubt')->_('联系人手机'),
            'label' => app::get('sysshoppubt')->_('联系人手机'),
            'order' => 4,
        ),


    'name' =>
        array (
            'is_title' => true,
            'type' => 'string',
            'length' => 50,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => true,
            'filterdefault' => 'true',
            'comment' => app::get('sysshoppubt')->_('样品名称'),
             'label' => app::get('sysshoppubt')->_('样品名称'),
            'order' => 5,
        ),

        'addr' => array(
            //'type' => 'varchar(60)',
            'type' => 'string',
            'length' => 90,
            'width' => 210,
            'default' => '',
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'comment' => app::get('sysshoppubt')->_('看样地点'),
             'label' => app::get('sysshoppubt')->_('看样地点'),
            'order' => 6,
        ),

        'about_time' => array(
            //'type' => 'varchar(60)',
            'type' => 'time',
            'width' => 100,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'comment' => app::get('sysshoppubt')->_('预约时间'),
             'label' => app::get('sysshoppubt')->_('预约时间'),
            'order' => 7,
        ),


        'desc' => array(
            //'type' => 'varchar(60)',
            'type' => 'text',
            'width' => 200,
            'default' => '',
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'filtertype' => 'normal',
            'comment' => app::get('sysshoppubt')->_('备注'),
            'label' => app::get('sysshoppubt')->_('备注'),
            'order' => 8,
        ),


        'state' => array(
            'type' => array(
                '0' => app::get('sysshoppubt')->_('未组织'),
                '1' => app::get('sysshoppubt')->_('已组织'),
            ),
            'default' => '0',
            'comment' => app::get('sysshoppubt')->_('状态'),
            'label' => app::get('sysshoppubt')->_('状态'),
        ),


        

       'shop_id' => array(
            'type' => 'table:shop@sysshop',
            'required' => true,
            'comment' => app::get('sysshoppubt')->_('所属企业id'),
        ),

        'shop_name'=>array(
            //'type'=>'varchar(50)',
            'type' => 'string',
            'length' => 190,
            'in_list'=>true,
            'width' => 110,
            'label' => app::get('sysshoppubt')->_('所属企业名称'),
            'comment' => app::get('sysshoppubt')->_('所属企业名称'),
        ),

        'user_id' => array(
            'type' => 'string',
            'length' => 50,
            'comment' => app::get('sysshoppubt')->_('参加企业的id'),
        ),

        'user_name' => array(
            'type' => 'string',
            'length' => 190,
            'in_list'=>true,
            'width' => 110,
            'label' => app::get('sysshoppubt')->_('参加企业的名称'),
            'comment' => app::get('sysshoppubt')->_('参加企业的名称'),
        ),

        'create_time' => array(
            'type' => 'time',
            'comment' => app::get('sysshoppubt')->_('提交时间'),
             'label' => app::get('sysshoppubt')->_('提交时间'),
            'order' => 11,
        ),
    ),
    'primary' => 'seegoods_id',
    'comment' => app::get('sysshoppubt')->_('看样表'),
);
