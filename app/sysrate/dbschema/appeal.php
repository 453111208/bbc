<?php

return  array(
    'columns' => array(
        'appeal_id' => array(
            //'type' => 'bigint unsigned',
            'type' => 'bigint',
            'unsigned' => true,
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'comment' => app::get('sysrate')->_('申诉ID'),
        ),
        'rate_id' => array(
            //'type' => 'bigint unsigned',
            'type' => 'bigint',
            'unsigned' => true,

            'required' => true,
            'comment' => app::get('sysrate')->_('评价ID'),
        ),
        'status' => array(
            'type' => array('WAIT' => '等待批准','REJECT' => '申诉驳回','SUCCESS' => '申诉成功','CLOSE' => '申诉关闭',),
            'default' => 'WAIT',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysrate')->_('申诉状态'),
        ),
        'appeal_type' => array(
            'type' => ['APPLY_DELETE'=>'申请删除评论','APPLY_UPDATE'=>'申请修改评论'],
            'default' => 'APPLY_UPDATE',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysrate')->_('申诉类型'),
        ),
        'appeal_again' => array(
            'type' => 'bool',
            'default' => 0,
            'comment' => app::get('sysrate')->_('再次申诉'),
        ),
        'content' => array(
            'type' => 'text',
            'default' => '',
            'label' => app::get('sysrate')->_('申诉内容'),
            'in_list' => true,
            'default_in_list' => false,
        ),
        'evidence_pic' =>
        array(
            //'type' => 'varchar(255)',
            'type' => 'text',
            'comment' => app::get('sysrate')->_('申诉图片凭证'),
        ),
        'reject_reason' => array(
            //'type' => 'varchar(255)',
            'type' => 'text',
            'default' => '',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysrate')->_('驳回理由'),
        ),
        'appeal_log' => array(
            'type' => 'serialize',
            'default' => '',
            'comment' => app::get('sysrate')->_('申诉日志'),
        ),
        'appeal_time' =>
        array(
            'type' => 'time',
            'label' => app::get('sysrate')->_('申诉时间'),
            'width' => '100',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'modified_time' => array(
            'type' => 'last_modify',
            'label' => app::get('sysrate')->_('最后修改时间'),
            'in_list' => true,
            'default_in_list' => true,
            'width' => '100',
        ),
    ),

    'primary' => 'appeal_id',
    'comment' => app::get('sysrate')->_('评论申诉表'),
);
