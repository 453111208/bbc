<?php
return  array(
    'columns' => array(
        'grade_id' => array(
            'type' => 'number',
            //'pkey' => true,
            'autoincrement' => true,
            'required' => true,
            'label' => app::get('sysuser')->_('等级id'),
        ),
        'grade_name' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'required' => true,
            'default' => "",
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysuser')->_('等级名称'),
        ),
        'grade_logo' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'comment' => app::get('sysuser')->_('会员等级LOGO'),
        ),
        'experience' => array(
            //'type' => 'int',
            'type' => 'number',
            'required' => true,
            'default' => 0,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysuser')->_('所需成长值'),
        ),
        'default_grade' => array(
            'type' => 'bool',
            'required' => true,
            'default' =>'0',
            'in_list' => false,
            'default_in_list' => false,
            'comment' => app::get('sysuser')->_('是否默认等级'),
        ),
        'validity' => array(
            'type' => 'time',
            'required' => true,
            'default' =>'0',
            'in_list' => true,
            'default_in_list' => false,
            'comment' => app::get('sysuser')->_('等级有效期'),
            'label' => app::get('sysuser')->_('等级有效期'),
        ),
   ),

    'primary' => 'grade_id',
    'index' => array(
        'ind_experience' => ['columns' => ['experience']],
    ),

    'comment' => app::get('sysuser')->_('会员等级表'),
);
