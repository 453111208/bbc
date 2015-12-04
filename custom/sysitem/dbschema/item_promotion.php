<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.com/license/gpl GPL License
 */

//商品关联的促销表，方便搜索
return  array(
    'columns' => array(
        'item_id' => array(
            'type' => 'number',
            'required' => true,
            'comment' => app::get('sysitem')->_('商品ID'),
        ),
        'promotion_id' => array(
            'type' => 'number',
            'required' => true,
            'comment' => app::get('sysitem')->_('促销id'),
        ),
    ),

    'primary' => ['item_id', 'promotion_id'],
    'comment' => app::get('sysitem')->_('商品关联的促销表(新)'),
);
