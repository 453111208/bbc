<?php
return  array(
    'columns' => array(
        'id' => array(
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'comment' => app::get('site')->_('ID'),
        ),
        'app' => array(
            'type' => 'string',
            'length' => 50,
            'default' => '',
            'required' => true,
            'label' => app::get('site')->_('程序目录'),
            'width' => 80,
            'default_in_list' => true,
            'in_list' => true,
            'comment' => app::get('site')->_('应用(app)'),
        ),
        'route' => array(
            'type' => 'string',
            'length' => 50,
            'default' => '',
            'required' => true,
            'label' => app::get('site')->_('唯一标示'),
            'comment' => app::get('site')->_('路由别名，唯一标示'),
            'width'=> 80,
            'default_in_list' => true,
            'in_list' => true,
        ),
        'title' => array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'default' => '',
            'required' => true,
            'label' => app::get('site')->_('名称'),
            'width' => 100,
            'default_in_list' => true,
            'in_list' => true,
        ),
        'config' => array(
            'type' => 'serialize',
            'default' => '',
            'label' => app::get('site')->_('配置'),
        ),
        'param' => array(
            'type' => 'serialize',
            'default' => '',
            'label' => app::get('site')->_('参数'),
        ),
        'update_modified' =>  array(
            'type' => 'time',
            'editable' => false,
            'comment' => app::get('site')->_('更新时间'),
        ),
    ),

    'primary' => 'id',
    'comment' => app::get('site')->_('前台SEO配置表'),
);
