<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array (
    'columns' =>
    array (
        'menu_id'=>array(
            'type' => 'number',
            //'pkey' => true,
            'autoincrement' => true,
            'comment' => app::get('desktop')->_('后台菜单ID'),
        ),
        'menu_type' =>
        array (
            //'type' => 'varchar(80)',
            'type' => 'string',
            'length' => 80,
            'required' => true,
            'width' => 100,
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('desktop')->_('菜单类型'),
        ),
        'app_id' =>
        array (
            'type' => 'table:apps@base',
            'required' => true,
            'width' => 100,
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('desktop')->_('所属app(应用)ID'),
        ),
        'workground'=>array(
            //'type'=>'varchar(200)',
            'type' => 'string',
            'length' => 200,
            'comment' => app::get('desktop')->_('顶级菜单'),
        ),
        'menu_group'=>array(
            //'type'=>'varchar(200)',
            'type' => 'string',
            'length' => 200,
            'comment' => app::get('desktop')->_('菜单组'),
        ),
        'menu_title'=>array(
            //'type'=>'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'is_title'=>true,
            'comment' => app::get('desktop')->_('菜单标题'),
        ),
        'menu_path'=>array(
            //'type'=>'varchar(255)',
            'type' => 'string',
            'comment' => app::get('desktop')->_('菜单对应执行的url路径'),
        ),
        'disabled'=>array(
            'type'=>'bool',
            'default'=>0
        ),
        'display'=>array(
            'type' => 'bool',
            //'type'=>"enum('true', 'false')",
            'default'=>0,
            'comment' => app::get('desktop')->_('是否显示'),
        ),
        'permission'=>array(
            //'type'=>'varchar(80)',
            'type' => 'string',
            'length' => 80,
            'comment' => app::get('desktop')->_('权限,有效显示范围'),
        ),
        'addon'=>array(
            'type'=>'text',
            'comment' => app::get('desktop')->_('额外信息'),
        ),
        'target'=>array(
            //'type'=>'varchar(10)',
            'type' => 'string',
            'length' => 10,
            'default'=>'',
            'comment' => app::get('desktop')->_('跳转'),
        ),
        'menu_order'=>array(
            'type' => 'number',
            'default'=>'0',
            'comment' => app::get('desktop')->_('排序'),
        ),
        'parent'=>array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'default'=>'0',
            'comment' => app::get('desktop')->_('父节点'),
        ),
    ),
    'primary' => 'menu_id',
    'index' => array(
        'ind_menu_type' => ['columns'=>['menu_type']],
        'ind_menu_path' => ['columns'=>['menu_path']],
        'ind_menu_order' => ['columns'=>['menu_order']],
    ),
    'unbackup' => true,
    'comment' => app::get('desktop')->_('后台菜单表'),
);
