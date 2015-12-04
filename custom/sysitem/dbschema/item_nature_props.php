<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return  array(
    'columns' => array(
        'item_id' => array(
            'type' => 'table:item',
            //'pkey' => true,
            'default' => 0,
            'required' => true,
            'editable' => false,
            'comment' => app::get('sysitem')->_('商品ID'),
        ),
        // 'cat_id' => array(
        //   'type' => 'table:cat@syscategory',
        //   'default' => 0,
        //   'required' => true,
        //   'editable' => false,
        //   'comment' => app::get('sysitem')->_('商品三级分类ID'),
        // ),
        'prop_id' => array(
            'type' => 'table:props@syscategory',
            //'pkey' => true,
            'default' => 0,
            'required' => true,
            'editable' => false,
            'comment' => app::get('sysitem')->_('自然属性ID'),
        ),
        'prop_value_id' => array(
            'type' => 'table:prop_values@syscategory',
            'default' => 0,
            'required' => true,
            'editable' => false,
            'comment' => app::get('sysitem')->_('自然属性值ID'),
        ),
        'pv_type' => array(
            'type' => array(
                'select' => app::get('sysitem')->_('下拉框select'),
                'text' => app::get('sysitem')->_('输入值text'),
            ),
            'default' => 'select',
            'required' => true,
            'label' => app::get('sysitem')->_('商品人员输入或选择的属性值的值'),
            'editable' => false,
        ),
        'pv_number' => array(
            'type' => 'number',
            'editable' => false,
        ),
        'pv_str' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'editable' => false,
        ),
        'modified_time' => array(
            'type' => 'last_modify',
            'label' => app::get('sysitem')->_('更新时间'),
            'width' => 110,
            'in_list' => true,
            'orderby' => true,
        ),
    ),
    
    'primary' => ['item_id', 'prop_id'],
    'comment' => app::get('sysitem')->_('商品自然属性信息表'),
);
