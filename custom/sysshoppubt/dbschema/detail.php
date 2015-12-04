<?php
// 商品招标
return array (
    'columns' =>
    array (
        'detail_id' =>
        array (
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('招标规则细分主键'),
        ),

        'tenderrule_id'=>array(
            'type' => 'string',
            'label' => app::get('sysshoppubt')->_('选用规则id'),
            'comment' => app::get('sysshoppubt')->_('选用规则id'),
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
        'create_time' => array(
            'type' => 'time',
            'label' => app::get('sysshoppubt')->_('提交时间'),
            'comment' => app::get('sysshoppubt')->_('提交时间'),
            'in_list' => true,
            'default_in_list' => false,
            'order'=>5,
        ),
    ),
    'primary' => 'detail_id',
    'comment' => app::get('sysshoppubt')->_('招标规则细分主键'),
);
