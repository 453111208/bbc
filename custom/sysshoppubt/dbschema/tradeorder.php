<?php
// 交易单
return array (
    'columns' =>
    array (
        'tradeorder_id' =>
        array (
            //'type' => 'int(10)',
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('交易单id'),
        ),
        'standard_id' =>
        array (
            'type' => 'number',
            'comment' => app::get('sysshoppubt')->_('标准商品id'),
        ),

       'bidding_id' =>
        array (
            'type' => 'number',
            'comment' => app::get('sysshoppubt')->_('竞价商品id'),
        ),

        'tender_id' =>
        array (
            'type' => 'number',
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


        'info_uniqid' => array(
            'type' => 'string',
            'length' => 190,
            'comment' => app::get('sysshoppubt')->_('标准商品发布表uniqid'),
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
            'label' => app::get('sysshoppubt')->_('卖方'),
            'order' => 2,
        ),

        
        'user_id' => array(
            'type' => 'string',
            'length' => 50,
            'required' => true,
            'comment' => app::get('sysshoppubt')->_('买方id'),
             'label' => app::get('sysshoppubt')->_('买方id'),
        ),

        'user_name'=>array(
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
            'label' => app::get('sysshoppubt')->_('买方'),
            'order' => 3,
        ),

       'fixed_price'=>
         array (
            'type' => 'money',
           'comment' => app::get('sysshoppubt')->_('一口价'),
           'label' => app::get('sysshoppubt')->_('一口价'),
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'order' => 4,
        ),

        'totalbid'=>
         array (
            'type' => 'money',
            'default' => '0',
           'comment' => app::get('sysshoppubt')->_('总价'),
           'label' => app::get('sysshoppubt')->_('总价'),
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'order' => 5,
       ),

        'type' => array(
            'type' => array(
                0 => app::get('sysshoppubt')->_('标准'),
                1 => app::get('sysshoppubt')->_('竞价'),
                2 => app::get('sysshoppubt')->_('招标'),
            ),
            'default' => 0,
            'comment' => app::get('sysshoppubt')->_('类型'),
            'label' => app::get('sysshoppubt')->_('类型'),
            'width' => 110,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'order' => 6,
        ),

        'state' => array(
            'type' => array(
                '0' => app::get('sysshoppubt')->_('待观察'),
                '1' => app::get('sysshoppubt')->_('成功'),
                '2' => app::get('sysshoppubt')->_('失败'),
                '3' => app::get('sysshoppubt')->_('有意向'),
            ),
            'default' => '0',
            'label' => app::get('sysshoppubt')->_('交易单状态'),
            'width' => 110,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'filtertype' => true,
            'filterdefault' => 'true',
            'order' => 7,
        ),

        'create_time' => array(
            'type' => 'time',
            'label' => app::get('sysshoppubt')->_('提交时间'),
            'comment' => app::get('sysshoppubt')->_('提交时间'),
            'in_list' => true,
            'default_in_list' => false,
            'order'=>8,
        ),
    ),
    'primary' => 'tradeorder_id',
    'comment' => app::get('sysshoppubt')->_('交易单'),
);
