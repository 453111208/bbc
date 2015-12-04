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
        'cat_id' =>
        array (
            'type' => 'table:cat@syscategory',
            'default' => 0,
            'required' => true,
            'editable' => false,
            'comment' => app::get('sysitem')->_('商品三级分类ID'),
        ),
        'prop_id' =>
        array (
            'type' => 'table:props@syscategory',
            'default' => 0,
            'required' => true,
            'editable' => false,
            'comment' => app::get('sysitem')->_('销售属性ID'),
        ),
        'prop_value_id' =>
        array (
            'type' => 'table:prop_values@syscategory',
            'default' => 0,
            'required' => true,
            //'pkey' => true,
            'editable' => false,
            'comment' => app::get('sysitem')->_('销售属性值ID'),
        ),
        'item_id' =>
        array (
            'type' => 'table:item',
            'default' => 0,
            'required' => true,
            'editable' => false,
            'comment' => app::get('sysitem')->_('商品ID'),
        ),
        'sku_id' =>
        array (
            'type' => 'table:sku',
            'default' => 0,
            'required' => true,
            //'pkey' => true,
            'editable' => false,
            'comment' => app::get('sysitem')->_('sku ID'),
        ),
        'modified_time' =>
        array (
            'type' => 'last_modify',
            'label' => app::get('sysitem')->_('更新时间'),
            'width' => 110,
            'in_list' => true,
            'orderby' => true,
        ),
    ),
    'primary' => ['prop_value_id', 'sku_id'],
    'comment' => app::get('sysitem')->_('商品规格索引表'),
);
