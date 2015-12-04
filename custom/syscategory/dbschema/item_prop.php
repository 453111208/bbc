<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return  array(
    'columns' => array(
        'item_prop_id' => array(
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'comment' => app::get('syscategory')->_('商品属性id'),
        ),
        'item_prop_name' => array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'required' => true,
            'default' => '',
            'order' => 10,
            'label' => app::get('syscategory')->_('商品属性名称'),
            'width' => 180,
            'editable' => true,
            'in_list' => true,
            'is_title' => true,
            'default_in_list' => true,
        ),


        'order_sort' => array(
            'type' => 'number',
            'default' => 1,
            'order' => 50,
            'required' => true,
            'editable' => false,
            'deny_export' => true,
            'label' => app::get('syscategory')->_('排序'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'modified_time' => array(
            'type' => 'last_modify',
            'label' => app::get('syscategory')->_('更新时间'),
            'width' => 110,
            'order' => 50,
            'editable' => false,
            'in_list' => true,
            'default_in_list' => false,
        ),
    ),
    
    'primary' => 'item_prop_id',
    'comment' => app::get('syscategory')->_('商品属性表'),
);
