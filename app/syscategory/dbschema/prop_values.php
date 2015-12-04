<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return  array(
    'columns' => array(
        'prop_value_id' => array(
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('syscategory')->_('属性值ID'),
        ),
        'prop_id' => array(
            'type' => 'table:props',
            'default' => 0,
            'required' => true,
            'editable' => false,
            'comment' => app::get('syscategory')->_('属性ID'),
        ),
        'prop_value' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'default' => '',
            'required' => true,
            'editable' => false,
            'is_title' => true,
            'comment' => app::get('syscategory')->_('属性值'),
        ),
        'prop_image' => array(
            'type' => 'string',
            'default' => '',
            'editable' => false,
            'comment' => app::get('syscategory')->_('属性图片'),
        ),
        'order_sort' => array(
            'type' => 'number',
            'default' => 0,
            'required' => true,
            'comment' => app::get('syscategory')->_('排序'),
        ),
    ),

    'primary' => 'prop_value_id',
    'comment' => app::get('syscategory')->_('属性值表'),
);
