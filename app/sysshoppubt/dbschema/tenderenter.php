<?php
// 商品招标
return array (
    'columns' =>
    array (
        'tenderenter_id' =>
        array (
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('投标干预主键'),
        ),
        'tender_man_id' => array(
            'type' => 'string',
            'default' => '0',
            //'pkey' => true,
            'comment' => app::get('sysshoppubt')->_('投标企业ID'),
        ),

        'tender_id' => array(
            'type' => 'string',
            'default' => '0',
            //'pkey' => true,
            'comment' => app::get('sysshoppubt')->_('交易ID'),
        ),
        
        'tender_title' => array(
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
        'tender_man' => array(
            //'type' => 'varchar(60)',
            'type' => 'string',
            'length' => 90,
            'width' => 210,
            'default' => '',
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'comment' => app::get('sysshoppubt')->_('投标企业名称'),
            'label' => app::get('sysshoppubt')->_('投标企业名称'),
            'order' => 2,
        ),
        'price' => array(
            'type' => 'money',
            'default' => '0',
            //'pkey' => true,
            'label' => app::get('sysshoppubt')->_('投标价格'),
            'comment' => app::get('sysshoppubt')->_('投标价格'),
            'order' => 5,
        ),
        'name' => array(
            'type' => 'string',
            'length' => 80,
            'label' => app::get('sysshoppubt')->_('操作人'),
            'comment' => app::get('sysshoppubt')->_('操作人'),
            'in_list' => true,
            'default_in_list' => false,
            'order' => 3,
        ),
        'score' => array(
            'type' => 'string',
            'length' => 80,
            'label' => app::get('sysshoppubt')->_('评分'),
            'comment' => app::get('sysshoppubt')->_('评分'),
            'in_list' => true,
            'default_in_list' => true,
            'order' => 6,
        ),
        'tender_time' => array(
            'type' => 'time',
            'label' => app::get('sysshoppubt')->_('投标时间'),
            'comment' => app::get('sysshoppubt')->_('投标时间'),
            'in_list' => true,
            'default_in_list' => false,
            'order'=>4,
        ),
        'openornot' => array(
            'type' => array(
                0 => app::get('sysshoppubt')->_('是'),
                1 => app::get('sysshoppubt')->_('否'),
            ),
            'default' => 0,
            'comment' => app::get('sysshoppubt')->_('是否开启'),
        ),
        'winornot' => array(
            'type' => array(
                0 => app::get('sysshoppubt')->_('是'),
                1 => app::get('sysshoppubt')->_('否'),
                2 => app::get('sysshoppubt')->_('尚未确定'),
            ),
            'default' => 2,
            'label' => app::get('sysshoppubt')->_('是否中标'),
            'comment' => app::get('sysshoppubt')->_('是否中标'),
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
    'primary' => 'tenderenter_id',
    'comment' => app::get('sysshoppubt')->_('投标干预'),
);
