<?php
// 交易单明细 
return array (
    'columns' =>
    array (
    'torderitem_id' =>
        array (
            //'type' => 'int(10)',
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('交易单明细id'),
        ),

    'tradeorder_id' =>
        array (
            'type' => 'number',
            'comment' => app::get('sysshoppubt')->_('交易单id'),
        ),

    'item_id' => array(
            'type' => 'string',
            'length' => 90,
            'comment' => app::get('sysshoppubt')->_('商品id'),
       ),

    'bid'=>
         array (
            'type' => 'money',
            'default' => '0',
           'comment' => app::get('sysshoppubt')->_('出价'),
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'order' => 2,
       ),

    'create_time' => array(
            'type' => 'time',
            'comment' => app::get('sysshoppubt')->_('提交时间'),
        ),
    ),
    'primary' => 'torderitem_id',
    'comment' => app::get('sysshoppubt')->_('交易单'),
);
