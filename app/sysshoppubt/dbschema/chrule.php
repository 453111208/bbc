<?php
// 商品招标
return array (
    'columns' =>
    array (
        'chrule_id' =>
        array (
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('招标规则选用记录主键'),
        ),

        'tenderrule_id'=>array(
            'type' => 'string',
            'label' => app::get('sysshoppubt')->_('规则id'),
            'comment' => app::get('sysshoppubt')->_('规则id'),
        ),

        'uniqid'=>array(
            'type' => 'string',
            'length' => 190,
            'label' => app::get('sysshoppubt')->_('id'),
            'comment' => app::get('sysshoppubt')->_('id'),
        ),

        'category' => array(
            'type' => 'string',
            'length' => 200,
            'comment' => app::get('sysshoppubt')->_('大类'),
            'label' => app::get('sysshoppubt')->_('大类'),
            'width' => 110,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'order' => 1,
        ),

        'project'=>array(
            'type' => 'string',
            'length' => 200,
            'in_list'=>true,
            'width' => 110,
            'default_in_list'=>true,
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => true,
            'filterdefault' => 'true',
            'label' => app::get('sysshoppubt')->_('项目'),
             'label' => app::get('sysshoppubt')->_('项目'),
            'order' => 2,
        ),
        'detail' => array(
            'type' => 'string',
            'length' => 200,
            'in_list'=>true,
            'width' => 110,
            'default_in_list'=>true,
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => true,
            'filterdefault' => 'true',
            'comment' => app::get('sysshoppubt')->_('细分'),
             'label' => app::get('sysshoppubt')->_('细分'),
            'order' =>3,
        ),
        'score' => array(
            'type' => 'number',
            'default' => 0,
            'comment' => app::get('sysshoppubt')->_('分数'),
            'label' => app::get('sysshoppubt')->_('分数'),
            'order' =>4,
        ),
        
        'type' => array(
            'type' => array(
                '0' => app::get('sysshoppubt')->_('文件'),
                '1' => app::get('sysshoppubt')->_('文本框'),
            ),
            'default' => '0',
            'comment' => app::get('sysshoppubt')->_('类型'),
            'label' => app::get('sysshoppubt')->_('类型'),
            'width' => 110,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
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
    'primary' => 'chrule_id',
    'comment' => app::get('sysshoppubt')->_('招标规则选用记录主键'),
);
