<?php
return  array(
    'columns' => array(
        'sku_id' => array(
            'type' => 'number',
            //'pkey' => true,
            'autoincrement' => true,
            'required' => true,
            'comment' => app::get('sysitem')->_('sku_id'),
        ),
        'item_id' => array(
            'type' => 'table:item',
            'required' => true,
            'comment' => app::get('sysitem')->_('商品id'),
        ),
        'title' => array(
            //'type' => 'varchar(60)',
            'type' => 'string',
            'length' => 90,
            'required' => true,
            'default'=>'',
            'label' => app::get('sysitem')->_('商品标题'),
            'comment' => app::get('sysitem')->_('商品标题'),
            'is_title' => true,
            'in_list' => true,
            'default_in_list' => true,
            'searchtype' => 'has',
            'filtertype' => 'custom',
            'filterdefault' => true,
            'order' => 12,
        ),
        'bn' => array(
            //'type' => 'varchar(30)',
            'type' => 'string',
            'length' => 30,
            'is_title'=>true,
            'required' => true,
            'comment' => app::get('sysitem')->_('商品编号'),
         ),
        'price' => array(
            'type' => 'money',
            'required' => true,
            'comment' => app::get('sysitem')->_('商品价格'),
        ),
        'cost_price' =>array (
            'type' => 'money',
            'default' => '0',
            'comment' => app::get('sysitem')->_('成本价'),
        ),
        'mkt_price' =>array (
            'type' => 'money',
            'default' => '0',
            'comment' => app::get('sysitem')->_('市场价'),
        ),
        'barcode' => array(
            //'type' => 'varchar(32)',
            'type' => 'string',
            'length' => 32,
            'comment' => app::get('sysitem')->_('条形码'),
        ),
        'weight' => array(
            //'type' => 'decimal(20,3)',
            'type' => 'decimal',
            'precision' => 20,
            'scale' =>3,

            'required' => true,
            'default' => 0,
            'label' => app::get('sysitem')->_('商品重量'),
            'comment' => app::get('sysitem')->_('商品重量'),
            'in_list' => true,
            'default_in_list' => false,
        ),
        'created_time' => array(
            'type' => 'time',
            'comment' => app::get('sysitem')->_('创建时间'),
        ),
        'modified_time' => array(
            'type' => 'last_modify',
            'comment' => app::get('sysitem')->_('最后更新时间'),
        ),
        'properties' => array(
            'type' => 'text',
            'comment' => app::get('sysitem')->_('sku销售属性'),
        ),
        'spec_info' => array (
            'type' => 'text',
            'comment' => app::get('sysitem')->_('物品描述'),
        ),
        'spec_desc' => array(
          'type' => 'serialize',
          'label' => app::get('sysitem')->_('规格值,序列化'),
        ),
        'status' => array(
            'type' => array(
                'normal' => '正常',
                'delete' => '删除',
            ),
            'default' => 'normal',
            'comment' => app::get('sysitem')->_('sku状态'),
        ),
        'outer_id' => array(
            //'type' => 'varchar(32)',
            'type' => 'string',
            'length' => 32,
            'comment' => app::get('sysitem')->_('商家设置的外部id'),
        ),
    ),

    'primary' => 'sku_id',
    'comment' => app::get('sysitem')->_('货品表'),
);
