<?php
// 商品竞价
return array (
    'columns' =>
    array (
        'biddingsitems_id' =>
        array (
            //'type' => 'int(10)',
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('参与竞价明细'),
        ),
        'biddings_id' => array(
            'type' => 'string',
            'length' => 190,
            'comment' => app::get('sysshoppubt')->_('竞价id'),
        ),
       'shop_id' => array(
            'type' => 'table:shop@sysshop',
            'required' => true,
            'comment' => app::get('sysshoppubt')->_('所属商家'),
             'label' => app::get('sysshoppubt')->_('所属商家'),
        ),

      'item_id' => array(
            'type' => 'string',
            'length' => 90,
            'comment' => app::get('sysshoppubt')->_('商品id'),
        ),
    'standardg_item_id' => array(
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

         'shop_name'=>array(
            //'type'=>'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'in_list'=>true,
            'width' => 110,
            'default_in_list'=>true,
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => true,
            'filterdefault' => 'true',
            'label' => app::get('sysshoppubt')->_('店铺名称'),
             'label' => app::get('sysshoppubt')->_('店铺名称'),
            'order' => 13,
        ),

        'price' => array(
            'type' => 'money',
            'default' => '0',
            'comment' => app::get('sysshoppubt')->_('商品价格'),
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'order' => 3,
        ),


        'net_price'=>
         array (
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('底价'),
             'label' => app::get('sysshoppubt')->_('底价'),
            'width' => 110,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'filtertype' => true,
            'filterdefault' => 'true',
            'order' => 7,
        ),
         'fixed_price'=>
         array (
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('一口价'),
             'label' => app::get('sysshoppubt')->_('一口价'),
            'width' => 110,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'filtertype' => true,
            'filterdefault' => 'true',
            'order' => 8,
        ),

        'b_price'=>
         array (
            'type' => 'money',
            'default' => '0',
            'required' => true,
            'editable' => false,
            'comment' => app::get('sysshoppubt')->_('竞价'),
             'label' => app::get('sysshoppubt')->_('竞价'),
            'width' => 110,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'filtertype' => true,
            'filterdefault' => 'true',
            'order' => 8,
        ),
    

      'create_time' => array(
            'type' => 'time',
            'label' => app::get('sysshoppubt')->_('提交时间'),
            'comment' => app::get('sysshoppubt')->_('提交时间'),
            'in_list' => true,
            'default_in_list' => false,
            'order'=>18,
        ),
  


    ),
    'primary' => 'biddingsitems_id',
    'comment' => app::get('sysshoppubt')->_('商品竞价明细'),
);
