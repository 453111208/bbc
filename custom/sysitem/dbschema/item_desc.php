<?php
return  array(
    'columns'=> array(
        'item_id' => array(
            'type' => 'table:item',
            //'pkey' => true,
            'required' => true,
            'comment' => app::get('sysitem')->_('商品id'),
        ),
        'pc_desc' => array(
            'type' => 'text',
            'comment' => app::get('sysitem')->_('宝贝详情'),
            'filtertype' => 'normal',
        ),
        'wap_desc' => array(
            'type' => 'text',
            'comment' => app::get('sysitem')->_('Wap宝贝详情'),
            'filtertype' => 'normal',
        ),
        'wireless_desc' => array(
            'type' => 'text',
            'comment' => app::get('sysitem')->_('无线端宝贝详情'),
            'filtertype' => 'normal',
        ),
    ),
    
    'primary' => 'item_id',
    'comment' => app::get('sysitem')->_('商品详情表'),
);

