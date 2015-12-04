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
        'detail_id' =>
        array (
            //'type' => 'int unsigned',
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            //'extra' => 'auto_increment',
            'editable' => false,
            'comment' => app::get('syslogistics')->_('序号'),
        ),
        'delivery_id' =>
        array (
            'type' => 'table:delivery@syslogistics',
            'required' => true,
            'default' => 0,
            'editable' => false,
            'comment' => app::get('syslogistics')->_('发货单号'),
        ),
        'oid' =>
        array (
            'type' => 'table:order@systrade',
            'required' => false,
            'default' => 0,
            'editable' => false,
            'comment' => app::get('syslogistics')->_('发货明细子订单号'),
        ),
        'item_type' =>
        array (
            'type' =>
            array (
                'item' => app::get('syslogistics')->_('商品'),
                'gift' => app::get('syslogistics')->_('赠品'),
                'pkg' => app::get('syslogistics')->_('捆绑商品'),
                'adjunct'=>app::get('syslogistics')->_('配件商品'),
            ),
            'default' => 'item',
            'required' => true,
            'editable' => false,
            'comment' => app::get('syslogistics')->_('商品类型'),
        ),
        'sku_id' =>
        array (
            //'type' => 'bigint unsigned',
            'type' => 'bigint',
            'unsigned' => true,
            
            'required' => true,
            'default' => 0,
            'editable' => false,
            'comment' => app::get('syslogistics')->_('SKU ID'),
        ),
        'sku_bn' =>
        array (
            //'type' => 'varchar(30)',
            'type' => 'string',
            'length' => 30,
            'editable' => false,
            'is_title' => true,
            'comment' => app::get('syslogistics')->_('sku编号'),
        ),
        'sku_title' =>
        array (
            //'type' => 'varchar(200)',
            'type' => 'string',
            'length' => 200,
            'editable' => false,
            'comment' => app::get('syslogistics')->_('sku名称'),
        ),
        'number' =>
        array (
            'type' => 'float',
            'required' => true,
            'default' => 0,
            'editable' => false,
            'comment' => app::get('syslogistics')->_('发货数量'),
        ),
    ),
    
    'primary' => 'detail_id',
    'comment' => app::get('syslogistics')->_('发货/退货单明细表'),
);
