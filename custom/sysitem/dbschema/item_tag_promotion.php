<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */

//商品关联的促销表，方便搜索
/*
promotion_ids字段
"$promotion_id1,$promotion_id2,......"
 */
return  array(
    'columns' => array(
        'item_id' => array(
            'type' => 'number',
            //'pkey' => true,
            'required' => true,
            'comment' => app::get('sysitem')->_('商品ID'),
        ),
        'promotion_ids' => array(
            'type' => 'text',
            'default' => '',
            'comment' => app::get('sysitem')->_('促销id信息'),
        ),
    ),
    
    'primary' => 'item_id',
    'comment' => app::get('sysitem')->_('商品关联的促销表'),
);
