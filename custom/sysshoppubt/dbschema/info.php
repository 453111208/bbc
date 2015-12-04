<?php
// 企业通知信息表
return array (
    'columns' =>
    array (
        'info_id' =>
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
        'info_uniqid' => array(
            'type' => 'string',
            'length' => 190,
            'comment' => app::get('sysshoppubt')->_('标准商品发布表uniqid'),
        ),

        'name' => array(
            'type' => 'string',
            'length' => 80,
            'comment' => app::get('sysshoppubt')->_('通知人'),
        ),
        
        'title' => array (
            'type' => 'text',
            'comment' => app::get('sysshoppubt')->_('通知标题'),
        ),

        'content' => array (
            'type' => 'text',
            'comment' => app::get('sysshoppubt')->_('通知内容'),
        ),

        'create_time' => array(
            'type' => 'time',
            'comment' => app::get('sysshoppubt')->_('提交时间'),
        ),
    ),
    'primary' => 'info_id',
    'comment' => app::get('sysshoppubt')->_('通知'),
);
