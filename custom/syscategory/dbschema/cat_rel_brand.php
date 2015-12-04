<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
return  array(
    'columns' => array(
        'cat_id' => array(
            'type' => 'table:cat',
            'required' => true,
            'default' => 0,
            //'pkey' => true,
            'editable' => false,
            'comment' => app::get('syscategory')->_('商品分类ID'),
        ),
        'brand_id' => array(
            'type' => 'table:brand',
            'required' => true,
            'default' => 0,
            //'pkey' => true,
            'editable' => false,
            'comment' => app::get('syscategory')->_('品牌ID'),
        ),
        'order_sort' => array(
            'type' => 'number',
            'editable' => false,
            'comment' => app::get('syscategory')->_('排序'),
        ),
    ),
    'primary' => ['cat_id', 'brand_id'],
    'comment' => app::get('syscategory')->_('分类和品牌关联表'),
);
