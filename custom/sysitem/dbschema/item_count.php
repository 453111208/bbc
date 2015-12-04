<?php
return  array(
    'columns'=> array(
        'item_id' => array(
            'type' => 'table:item',
            //'pkey' => true,
            'required' => true,
            'comment' => app::get('sysitem')->_('商品id'),
        ),
        'sold_quantity' => array(
            //'type' => 'int unsigned',
            'type' => 'number',
            'default' => 0,
            'label' => app::get('sysitem')->_('销量'),
            'comment' => app::get('sysitem')->_('商品销量'),
            'in_list' => true,
            'default_in_list' => false,
        ),
        'rate_count' =>array (
            //'type' => 'int unsigned',
            'type' => 'number',
            'default' => 0,
            'required' => true,
            'comment' => app::get('sysitem')->_('评论次数'),
        ),
        'rate_good_count' =>array (
            //'type' => 'int unsigned',
            'type' => 'number',
            'default' => 0,
            'required' => true,
            'comment' => app::get('sysitem')->_('好评次数'),
        ),
        'rate_neutral_count' =>array (
            //'type' => 'int unsigned',
            'type' => 'number',
            'default' => 0,
            'required' => true,
            'comment' => app::get('sysitem')->_('中评次数'),
        ),
        'rate_bad_count' =>array (
            //'type' => 'int unsigned',
            'type' => 'number',
            'default' => 0,
            'required' => true,
            'comment' => app::get('sysitem')->_('差评次数'),
        ),
        'view_count' =>array (
            //'type' => 'int unsigned',
            'type' => 'number',
            'length' => 80,
            'default' => 0,
            'required' => true,
            'comment' => app::get('sysitem')->_('浏览次数'),
        ),
        'buy_count' =>array (
            //'type' => 'int unsigned',
            'type' => 'number',
            'default' => 0,
            'required' => true,
            'comment' => app::get('sysitem')->_('购买次数'),
        ),
    ),
    
    'primary' => 'item_id',
    'comment' => app::get('sysitem')->_('商品次数表'),
);

