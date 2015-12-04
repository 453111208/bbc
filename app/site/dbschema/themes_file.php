<?php

return  array(
    'columns' => array(
        'id' => array(
            //'type' => 'int unsigned',
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('site')->_('模板文件ID'),
        ),
        'platform' => array(
            'type' => array(
                'pc' => '电脑端',
                'wap' => '无线端',
            ),
            'default' => 'pc',
            'required' => true,
            'label' => app::get('site')->_('模板终端'),
            'comment' => app::get('site')->_('模板终端'),
        ),
        'filename' => array(
            'type' => 'string',
            'comment' => app::get('site')->_('文件名'),
        ),
        'filetype' => array(
            //'type' => 'varchar(30)',
            'type' => 'string',
            'length' => 30,
            'comment' => app::get('site')->_('文件扩展名'),
        ),
        'fileuri' => array(
            'type' => 'string',
            'comment' => app::get('site')->_('文件路径标识,包括模板名. [theme name]:[filename]'),
        ),
        'version' => array(
            //'type' => 'integer',
            'type' => 'number',
            'required' => false,
            'comment' => app::get('site')->_('版本号'),
        ),
        'theme' => array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'comment' => app::get('site')->_('模板名标识'),
        ),
       # 'is_tmpl' => array(
       #     'type' => 'bool',
       #     'required' => true,
       #     'default'=>0,
       # ),
        'memo' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'comment' => app::get('site')->_('备注'),
        ),
        'content' => array(
            'type' => 'text',
            'comment' => app::get('site')->_('文件内容'),
        ),
    ),
    'primary' => 'id',
    'unbackup' => true,
    'comment' => app::get('site')->_('模板文件表'),
);
