<?php
// 商品总库存表，方便查询，只是商品，是所有sku的总和
return  array(
    'columns'=> array(
        'item_id' => array(
            'type' => 'table:item',
            'required' => true,
            //'pkey' => true,
            'comment' => app::get('sysitem')->_('商品 ID'),
        ),
        'store' => array(
            'type' => 'number',
            'required' => true,
            'default' => 0,
            'label' => app::get('sysitem')->_('总商品数量'),
            'comment' => app::get('sysitem')->_('总商品数量'),
        ),
        'freez' => array(
            'type' => 'number',
            'default' => 0,
            'comment' => app::get('sysitem')->_('sku预占库存总和'),
        ),
    ),
    
    'primary' => 'item_id',
    'comment' => app::get('sysitem')->_('商品总库存表'),
);

