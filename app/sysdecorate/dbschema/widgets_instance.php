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
        'widgets_id' =>
        array (
            //'type' => 'int unsigned',
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('sysdecorate')->_('挂件实例ID'),
        ),
        'shop_id' => array(
            'type'=>'table:shop@sysshop',
            'label' => app::get('sysdecorate')->_('店铺名称'),
            'required' => true,
        ),
        'widgets_type' =>
        array (
            //'type' => 'varchar(30)',
            'type' => 'string',
            'length' => 30,
            'required' => true,
            'default' => '',
            'editable' => false,
            'comment' => app::get('sysdecorate')->_('所属挂件的名称'),
        ),
        'theme' =>
        array (
            //'type' => 'varchar(30)',
            'type' => 'string',
            'length' => 30,
            'default' => '',
            'editable' => false,
            'comment' => app::get('sysdecorate')->_('模版的名称'),
        ),
        'title' =>
        array (
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'editable' => false,
            'comment' => app::get('sysdecorate')->_('挂件自定义标题'),
        ),
        'params' =>
        array (
            'type' => 'serialize',
            'editable' => false,
            'comment' => app::get('sysdecorate')->_('配置参数'),
        ),
        'modified_time' =>
        array (
            'type' => 'time',
            'editable' => false,
            'comment' => app::get('sysdecorate')->_('修改时间'),
        ),
    ),
    'primary' => 'widgets_id',
    'index' => array(
        'ind_wgbase' => ['columns' => ['shop_id', 'widgets_type']],
    ),
    'comment' => app::get('sysdecorate')->_('挂件实例表'),
);
