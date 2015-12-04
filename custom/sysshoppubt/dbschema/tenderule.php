<?php
// 商品招标
return array (
    'columns' =>
    array (
        'tenderrule_id' =>
        array (
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('招标规则主键'),
        ),
        'serial'=>array(
            'type' => 'number',
            'required' => true,
            'in_list'=>true,
            'width' => 110,
            'default_in_list'=>true,
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => true,
            'filterdefault' => 'true',
            'label' => app::get('sysshoppubt')->_('序号'),
            'comment' => app::get('sysshoppubt')->_('序号'),
            'order' => 1,
        ),

        'category'=>array(
            'type' => 'string',
            'length' => 200,
            'in_list'=>true,
            'width' => 110,
            'default_in_list'=>true,
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => true,
            'filterdefault' => 'true',
            'label' => app::get('sysshoppubt')->_('大类'),
             'label' => app::get('sysshoppubt')->_('大类'),
            'order' => 2,
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
            'order' => 3,
        ),


        'is_checkbox' => array(
            'type' => array(
                '0' => app::get('sysshoppubt')->_('否'),
                '1' => app::get('sysshoppubt')->_('是'),
            ),
            'default' => '0',
            'comment' => app::get('sysshoppubt')->_('是否勾选'),
            'label' => app::get('sysshoppubt')->_('是否勾选'),
            'width' => 110,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'order' => 5,
        ),
        'have_detail' => array(
            'type' => array(
                '0' => app::get('sysshoppubt')->_('否'),
                '1' => app::get('sysshoppubt')->_('是'),
            ),
            'default' => '0',
            'comment' => app::get('sysshoppubt')->_('是否有子细目'),
            'label' => app::get('sysshoppubt')->_('是否有子细目'),
            'width' => 110,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'order' => 9,
        ),
        'is_required' => array(
            'type' => array(
                '0' => app::get('sysshoppubt')->_('否'),
                '1' => app::get('sysshoppubt')->_('是'),
            ),
            'default' => '0',
            'comment' => app::get('sysshoppubt')->_('是否必选'),
            'label' => app::get('sysshoppubt')->_('是否必选'),
            'width' => 110,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'order' => 7,
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

        'state' => array(
            'type' => array(
                '1' => app::get('sysshoppubt')->_('发布'),
                '2' => app::get('sysshoppubt')->_('作废'),
                '3' => app::get('sysshoppubt')->_('未发布'),
            ),
            'default' => 1,
            'in_list'=>true,
            'width' => 110,
            'default_in_list'=>true,
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => true,
            'filterdefault' => 'true',
            'label' => app::get('sysshoppubt')->_('状态'),
            'order' => 9,
        ),


        'create_time' => array(
            'type' => 'time',
            'label' => app::get('sysshoppubt')->_('提交时间'),
            'comment' => app::get('sysshoppubt')->_('提交时间'),
            'in_list' => true,
            'default_in_list' => false,
            'order'=>10,
        ),
    ),
    'primary' => 'tenderrule_id',
    'comment' => app::get('sysshoppubt')->_('招标规则维护'),
);
