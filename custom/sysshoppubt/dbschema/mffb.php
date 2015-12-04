<?php
// 商品招标
return array (
    'columns' =>
    array (
        'mffb_id' =>
        array (
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('免费发布助手ID'),
        ),

        'lxfs'=>array(
            'type' => 'string',
            'label' => app::get('sysshoppubt')->_('联系方式'),
            'comment' => app::get('sysshoppubt')->_('联系方式'),
             'in_list' => true,
            'default_in_list' => true,
        ),
        'detail' => array(
            'type' => 'text',
            'comment' => app::get('sysshoppubt')->_('内容'),
             'label' => app::get('sysshoppubt')->_('内容'),
              'in_list' => true,
            'default_in_list' => true,
            'order' =>3,
        ),
        'create_time' => array(
            'type' => 'time',
            'label' => app::get('sysshoppubt')->_('提交时间'),
            'comment' => app::get('sysshoppubt')->_('提交时间'),
            'in_list' => true,
            'default_in_list' => true,
            'order'=>5,
        ),
    ),
    'primary' => 'mffb_id',
    'comment' => app::get('sysshoppubt')->_('发布助手表'),
);
