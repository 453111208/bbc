<?php
return  array(
    'columns'=> array(
        'item_id' => array(
            'type' => 'table:item',
            'required' => true,
            //'pkey' => true,
            'comment' => app::get('sysitem')->_('商品 ID'),
        ),
        'sku_id' => array(
            'type' => 'table:sku',
            'required' => true,
            //'pkey' => true,
            'comment' => app::get('sysitem')->_('sku ID'),
        ),
        'store' => array(
            'type' => 'number',
            'required' => true,
            'default' => 0,
            'label' => app::get('sysitem')->_('商品数量'),
            'comment' => app::get('sysitem')->_('商品数量'),
        ),
        'freez' => array(
            'type' => 'number',
            'default' => 0,
            'comment' => app::get('sysitem')->_('sku预占库存'),
        ),
    ),
    
    'primary' => ['item_id', 'sku_id'],
    'comment' => app::get('sysitem')->_('商品库存表'),
);
