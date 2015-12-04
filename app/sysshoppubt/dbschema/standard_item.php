<?php
//标准商品发布标(商品明细表)
return  array(
    'columns'=>array(
        'standardg_item_id' => array(
            'type' => 'number',
            //'pkey' => true,
            'autoincrement' => true,
            'required' => true,
            'comment' => app::get('sysshoppubt')->_('standardg_item_id'),
        ),
        
         'uniqid' => array(
            'type' => 'string',
            'length' => 190,
            'comment' => app::get('sysshoppubt')->_('标准商品发布表id'),
        ),

         'sku_id' => array(
            'type' => 'string',
            'length' => 90,
            'comment' => app::get('sysshoppubt')->_('sku_id'),
        ),

        'item_id' => array(
            'type' => 'string',
            'length' => 90,
            'comment' => app::get('sysshoppubt')->_('商品id'),
        ),

        'title' => array(
            //'type' => 'varchar(60)',
            'type' => 'string',
            'length' => 90,
            'required' => true,
            'default' => '',
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => true,
            'filterdefault' => 'true',
            'comment' => app::get('sysshoppubt')->_('商品名称'),
            'order' => 1,
        ),

        'bn' => array(
            'type' => 'string',
            'length' => 80,
            'required' => true,
            'comment' => app::get('sysshoppubt')->_('商品编号'),
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'filtertype' => true,
            'filterdefault' => 'true',
            'order' => 2,
         ),

        'price' => array(
            'type' => 'money',
            'default' => '0',
            'comment' => app::get('sysshoppubt')->_('商品价格'),
            
            'is_title' => true,
            'order' => 3,
        ),

        'advice' => array(
            'type' => 'money',
            'default' => '0',
            'comment' => app::get('sysshoppubt')->_('建议价格'),
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'order' => 6,
        ),

        'net_price'=>
         array (
            'type' => 'money',
            'default' => '0',
            'comment' => app::get('sysshoppubt')->_('底价'),
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'order' => 7,

        ),

         
         'fixed_price'=>
         array (
            'type' => 'money',
            'default' => '0',
           'comment' => app::get('sysshoppubt')->_('一口价'),
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'order' => 8,
        ),


        'num' => array(
            'type' => 'number',
            'comment' => app::get('sysshoppubt')->_('购买数量'),
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'order' => 4,
        ),

        'unit'=>array(
            'type' => 'string',
            'length' => 90,
            'comment' => app::get('sysshoppubt')->_('单位'),
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'filtertype' => true,
            'filterdefault' => 'true',
            'order' => 5,
        ),

    ),
    'primary' => 'standardg_item_id',
    'comment' => app::get('standard_item')->_('标准商品发布标(商品明细表)'),
);
