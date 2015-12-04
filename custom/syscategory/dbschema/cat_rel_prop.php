<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return  array(
    'columns' => array(
        'prop_id' => array(
            'type' => 'table:props',
            //'pkey' => true,
            'default' => 0,
            'editable' => false,
            'comment' => app::get('syscategory')->_('属性ID'),
        ),
        'cat_id' => array(
            'type' => 'table:cat',
            'default' => 0,
            //'pkey' => true,
            'editable' => false,
            'comment' => app::get('syscategory')->_('分类ID'),
        ),
        'order_sort'=>array(
            'type'=>'number',
            'default'=>0,
            'required'=>true,
            'editable'=>false,
        ),
    ),
    
    'primary' => ['prop_id', 'cat_id'],
    'comment' => app::get('syscategory')->_('商品属性表'),
);
