<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
return  array(
    'columns' => array(
        'id' => array(
            //'type' => 'int unsigned',
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('site')->_('页面模板ID'),
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
        'tmpl_type' => array(
            //'type' => 'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'required' => true,
            'comment' => app::get('site')->_('对应前台页面标示符'),
        ),
        'tmpl_name' => array(
            //'type' => 'varchar(30)',
            'type' => 'string',
            'length' => 30,
            'required' => true,
            'comment' => app::get('site')->_('名称'),
        ),
        'tmpl_path' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'required' => true,
            'comment' => app::get('site')->_('页面路径'),
        ),
        // 'version' => array(
        //     'type' => 'time',
        //     'required' => true,
        // ), 
        'theme' => array(
            //'type' => 'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'required' => true,
            'comment' => app::get('site')->_('对应模板'),
        ),
        // 'content' => array(
        //     'type' => 'text',
        // ),
        'rel_file_id' => array(
            'type' => 'number',
            'required' => true,
            'comment' => app::get('site')->_('关联模板文件表:site_themes_file'),
        ),
    ),
    'primary' => 'id',
    'unbackup' => true,
    'comment' => app::get('site')->_('页面模板表'),
);
