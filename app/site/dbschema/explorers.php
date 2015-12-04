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
            'comment' => app::get('site')->_('site地图表ID'),
        ),
        'app' => array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            
            'required' => true,
            'default' => '',
            'label' => app::get('site')->_('程序目录'),
            'width' => 80,
            'default_in_list' => true,
            'in_list' => true,
            'comment' => app::get('site')->_('应用(app)名'),
        ),
        'title' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'required' => true,
            'default' => '',
            'label' => app::get('site')->_('名称'),
            'width' => 120,
            'default_in_list' => true,
            'in_list' => true,
        ),
        'path' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'required' => true,
            'default' => '',
            'label' => app::get('site')->_('目录'),
            'width' => 120,
            'default_in_list' => true,
            'in_list' => true,
            'comment' => app::get('site')->_('路径'),
        ),
    ),
    
    'primary' => 'id',
    'comment' => app::get('site')->_('site地图表'),
);

