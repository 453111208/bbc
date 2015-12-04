<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2014-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array (
    'columns' =>
    array (
        'brand_id' =>
        array (
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'comment' => app::get('syscategory')->_('品牌id'),
        ),
        'brand_name' =>
        array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'label' => app::get('syscategory')->_('品牌名称'),
            'width' => 180,
            'is_title' => true,
            'required' => true,
            'order' => 10,
            'comment' => app::get('syscategory')->_('品牌名称'),
            'editable' => true,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'brand_alias' =>
        array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'label' => app::get('syscategory')->_('品牌别名'),
            'width' => 150,
            'order' => 20,
            'comment' => app::get('syscategory')->_('品牌别名'),
            'editable' => false,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'brand_url' =>
        array (
            //'type' => 'varchar(255)',
            'type' => 'string',
            
            'label' => app::get('syscategory')->_('品牌网址'),
            'width' => 350,
            'order' => 30,
            'comment' => app::get('syscategory')->_('品牌网址(保留字段)'),
            'editable' => true,
            'in_list' => true,
        ),
        'order_sort' =>
        array (
            'type' => 'number',
            'label' => app::get('syscategory')->_('排序'),
            'width' => 150,
            'order' => 40,
            'default' => 0,
            'comment' => app::get('syscategory')->_('排序'),
            'editable' => true,
            'in_list' => true,
            'default_in_list' => false,
        ),
        'brand_desc' =>
        array (
            //'type' => 'longtext',
            'type' => 'text',
            'comment' => app::get('syscategory')->_('品牌介绍(保留字段)'),
            'editable' => false,
        ),
        'brand_logo' =>
        array (
            //'type' => 'varchar(255)',
            'type' => 'string',
            'comment' => app::get('syscategory')->_('品牌图片标识'),
            'editable' => false,
            'label' => app::get('syscategory')->_('品牌图片标识'),
            'in_list' => false,
            'default_in_list' => false,
        ),
        'modified_time' =>
        array (
            'type' => 'last_modify',
            'label' => app::get('syscategory')->_('更新时间'),
            'width' => 110,
            'order' => 50,
            'editable' => false,
            'orderby' => true,
            'in_list' => true,
            'default_in_list' => false,
        ),
        'disabled' =>
        array (
            'type' => 'bool',
            'default' => 0,
            'comment' => app::get('syscategory')->_('失效'),
            'editable' => false,
            'label' => app::get('syscategory')->_('失效'),
            'in_list' => false,
            'deny_export' => true,
        ),

    ),
    'primary' => 'brand_id',
    'index' => array(
        'ind_disabled' => ['columns' => ['disabled']],
        'ind_ordernum' => ['columns' => ['order_sort']],
    ),
    'comment' => app::get('syscategory')->_('品牌表'),
);
