<?php
//标准商品发布表(Standard product release)
return  array(
    'columns'=>array(
        'standard_id' => array(
            'type' => 'number',
            //'pkey' => true,
            'autoincrement' => true,
            'required' => true,
            'comment' => app::get('sysshoppubt')->_('standard_id'),
        ),
        'shop_id' => array(
            'type' => 'table:shop@sysshop',
            'required' => true,
            'comment' => app::get('sysshoppubt')->_('所属企业'),
            'label' => app::get('sysshoppubt')->_('所属企业'),
        ),

        'uniqid'=>array(
            'type' => 'string',
            'length' => 190,
            'label' => app::get('sysshoppubt')->_('id'),
            'comment' => app::get('sysshoppubt')->_('id'),
        ),

        'trading_title' => array(
            //'type' => 'varchar(60)',
            'type' => 'string',
            'length' => 90,
            'width' => 210,
            'required' => true,
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
            'order' => 2,
        ),

        'price_type' => array(
            'type' => array(
                '1' => app::get('sysshoppubt')->_('价格公开'),
                '2' => app::get('sysshoppubt')->_('面议或电联'),
            ),
            'default' => '1',
            'label' => app::get('sysshoppubt')->_('价格方式'),
            'width' => 110,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'filtertype' => true,
            'filterdefault' => 'true',
            'order' => 3,
        ),

        'fund_trend' => array(
            'type' => array(
                '1' => app::get('sysshoppubt')->_('平台担保交易'),
                '2' => app::get('sysshoppubt')->_('自行线下支付'),
            ),
            'default' => '2',
            'label' => app::get('sysshoppubt')->_('资金走向'),
            'width' => 110,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'filtertype' => true,
            'filterdefault' => 'true',
            'order' => 4,
        ),

        'is_through' => array(
            'type' => array(
                1 => app::get('sysshoppubt')->_('是'),
                2 => app::get('sysshoppubt')->_('否'),
            ),
            'default' => 2,
            'comment' => app::get('sysshoppubt')->_('是否审核通过'),
            'label' => app::get('sysshoppubt')->_('是否审核通过'),
            'width' => 110,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'order' => 5,
        ),

        'through_time' => array(
            'type' => 'time',
            'label' => app::get('sysshoppubt')->_('审核通过时间'),
            'comment' => app::get('sysshoppubt')->_('审核通过时间'),
            'in_list' => true,
            'default_in_list' => false,
            'order'=>6,
        ),

        'create_time' => array(
            'type' => 'time',
            'label' => app::get('sysshoppubt')->_('提交时间'),
            'comment' => app::get('sysshoppubt')->_('提交时间'),
            'in_list' => true,
            'default_in_list' => false,
            'order'=>7,
        ),


        'desc' => array(
            'type' => 'text',
            'comment' => app::get('sysshoppubt')->_('详情'),
            'filtertype' => 'normal',
        ),

        'attendcount' => array(
            'type' => 'string',
            'length' => 10,
            'default' => 0,
            'comment' => app::get('sysshoppubt')->_('參加看样人数'),
            'label' => app::get('sysshoppubt')->_('參加看样人数'),

        ),


        'seegoods_stime' => array(
            'type' => 'time',
            'label' => app::get('sysshoppubt')->_('看样结束时间'),
            'comment' => app::get('sysshoppubt')->_('看样结束时间'),
            'in_list' => true,
            'default_in_list' => false,
            'order'=>8,
        ),



        'stoptime_str' => array(
            'type' => 'string',
            'length' => 100,
            'label' => app::get('sysshoppubt')->_('发布交易截至时间(字符串)'),
            'comment' => app::get('sysshoppubt')->_('发布交易截至时间(字符串)'),
        ),

        'stop_time' => array(
            'type' => 'time',
            'label' => app::get('sysshoppubt')->_('发布交易截至时间'),
            'comment' => app::get('sysshoppubt')->_('发布交易截至时间'),
            // 'in_list' => true,
            // 'default_in_list' => false,
            'order'=>7,
        ),
        'isok' => array( 
            'type' => array(
                0 => app::get('sysshoppubt')->_('待定'), 
                1 => app::get('sysshoppubt')->_('是'),
                2 => app::get('sysshoppubt')->_('否'),
            ),
            'default' => 0,
            'comment' => app::get('sysshoppubt')->_('是否成功'),
            'label' => app::get('sysshoppubt')->_('是否成功'),
            'width' => 110,

            'is_title' => true,
            'order' => 20,
        ),
    ),
    'primary' => 'standard_id',
    'comment' => app::get('sysshoppubt')->_('标准商品发布标'),
);
