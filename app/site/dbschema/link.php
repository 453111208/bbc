<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
return  array(
    'columns' => array(
        'link_id' => array(
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'comment' => app::get('site')->_('链接ID'),
        ),
        'link_name' => array(
            //'type' => 'varchar(128)',
            'type' => 'string',
            'length' => 128,
            'required' => true,
            'default' => '',
            'label' => app::get('site')->_('链接名称'),
            'width' => 100,
            'default_in_list' => true,
            'in_list' => true,
        ),
        'href' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'required' => true,
            'default' => '',
            'label' => app::get('site')->_('链接地址'),
            'width' => 180,
            'default_in_list' => true,
            'in_list' => true,
        ),
        'image_url' => array(
            'type' => 'string',
            'label' => app::get('site')->_('图片地址'),
            'width' => 120,
            'default_in_list' => false,
            'in_list' => false,
        ),
        'orderlist' => array(
            'type' => 'number',
            'default' => 0,   
            'label' => app::get('site')->_('排序'),
            'required' => true,
            'default_in_list' => true,
            'in_list' => true,
        ),
        'hidden' => array(
            //'type' => array('true'=>app::get('site')->_('是'), 'false'=>app::get('site')->_('否')),
            'type' => 'bool',
            'label' => app::get('site')->_('隐藏'),
            'required' => true,
            'default' => 0,
            'default_in_list' => true,
            'in_list' => true,
        ),
    ),
    
    'primary' => 'link_id',
    'comment' => app::get('site')->_('前台链接表'),
);
