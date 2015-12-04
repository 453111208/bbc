<?php
/**
* ShopEx licence
*
* @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
* @license  http://ecos.shopex.cn/ ShopEx License
*/

return  array(
    'columns' => array(
        'widgets_id' => array(
            //'type' => 'int unsigned',
            'type' => 'number',
            
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('site')->_('挂件实例ID'),
        ),
        // 'platform' => array(
        //     'type' => array(
        //         'pc' => '电脑端',
        //         'wap' => '无线端',
        //     ),
        //     'default' => 'pc',
        //     'required' => true,
        //     'label' => app::get('site')->_('模板终端'),
        //     'comment' => app::get('site')->_('模板终端'),
        // ),
        'core_file' => array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'required' => true,
            'default' => '',
            'editable' => false,
            'is_title' => true,
            'comment' => app::get('site')->_('挂件挂在模版的页面'),
        ),
        'core_slot' => array(
            //'type' => 'tinyint unsigned',
            'type' => 'smallint',
            'unsigned' => true,
            
            'default' => 0,
            'required' => true,
            'editable' => false,
            'comment' => app::get('site')->_('模版中提供给挂件位置序号'),
        ),
        'core_id' => array(
            //'type' => 'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'editable' => false,
            'comment' => app::get('site')->_('位置的ID'),
        ),
        'widgets_type' => array(
            //'type' => 'varchar(30)',
            'type' => 'string',
            'length' => 30,
            'required' => true,
            'default' => '',
            'editable' => false,
            'comment' => app::get('site')->_('所属挂件的名称'),
        ),
        'app' => array(
            //'type' => 'varchar(30)',
            'type' => 'string',
            'length' => 30,
            'default' => '',
            'editable' => false,
            'comment' => app::get('site')->_('所属的应用'),
        ),
        'theme' => array(
            //'type' => 'varchar(30)',
            'type' => 'string',
            'length' => 30,
            'default' => '',
            'editable' => false,
            'comment' => app::get('site')->_('模版的名称'),
        ),
        'widgets_order' => array(
            //'type' => 'tinyint unsigned',
            'type' => 'smallint',
            'unsigned' => true,
            
            'default' => 5,
            'required' => true,
            'editable' => false,
            'comment' => app::get('site')->_('挂件顺序'),
        ),
        'title' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'editable' => false,
            'comment' => app::get('site')->_('挂件自定义标题'),
        ),
        'domid' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'editable' => false,
            'comment' => app::get('site')->_('挂件id'),
        ),
        'classname' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'editable' => false,
            'comment' => app::get('site')->_('css class name'),
        ),
        'tpl' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'editable' => false,
            'comment' => app::get('site')->_('模版的名称'),
        ),
        'params' => array(
            'type' => 'serialize',
            'editable' => false,
            'comment' => app::get('site')->_('配置参数'),
        ),
        'modified' => array(
            'type' => 'time',
            'editable' => false,
            'comment' => app::get('site')->_('修改时间'),
        ),
    ),
    'index' => array(
        'ind_wgbase' => array(
            'columns' => array(
                0 => 'core_file',
                1 => 'core_id',
                2 => 'widgets_order',
            ),
        ),
        'ind_wginfo' => array(
            'columns' => array(
                0 => 'core_file',
                1 => 'core_slot',
                2 => 'widgets_order',
            ),
        ),
    ),
    'primary' => 'widgets_id',
    'unbackup' => true,
    'comment' => app::get('site')->_('挂件实例表'),
);
