<?php
// 审核意见
return array (
    'columns' =>
    array (
        'checks_id' =>
        array (
            //'type' => 'int(10)',
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('审核记录id'),
        ),

        'sprodrelease_id' =>
        array (
            'type' => 'string',
            'length' => 190,
            'comment' => app::get('sysshoppubt')->_('标准商品发布表id'),
        ),

       'biddings_id' =>
        array (
            'type' => 'string',
            'length' => 190,
            'comment' => app::get('sysshoppubt')->_('竞价id'),
        ),

    'tender_id' =>
        array (
            'type' => 'string',
            'length' => 190,
            'comment' => app::get('sysshoppubt')->_('招标id'),
        ),
        'sprodrelease_uniqid' => array(
            'type' => 'string',
            'length' => 190,
            'comment' => app::get('sysshoppubt')->_('标准商品发布表uniqid'),
        ),

        'name' => array(
            'type' => 'string',
            'length' => 80,
            'comment' => app::get('sysshoppubt')->_('审核人'),
        ),

        
        'is_through' => array(
            'type' => array(
                1 => app::get('sysshoppubt')->_('是'),
                2 => app::get('sysshoppubt')->_('否'),
            ),
            'default' => 2,
            'comment' => app::get('sysshoppubt')->_('是否审核通过'),
            'label' => app::get('sysshoppubt')->_('是否审核通过'),
        ),
        
        'text' => array (
            'type' => 'text',
            'comment' => app::get('sysshoppubt')->_('意见描述'),
        ),

        'create_time' => array(
            'type' => 'time',
            'comment' => app::get('sysshoppubt')->_('创建时间'),
        ),
    ),
    'primary' => 'checks_id',
    'comment' => app::get('sysshoppubt')->_('审核意见'),
);
