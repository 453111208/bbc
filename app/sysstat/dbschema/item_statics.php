<?php
return  array(
    'columns'=>array(
        'item_stat_id'=>array(
            //'type'=>'bigint unsigned',
            'type' => 'bigint',
            'unsigned' => true,
            //'pkey'=>true,
            'autoincrement' => true,
            'required' => true,
            'label' => 'id',
            'comment' => app::get('sysstat')->_('商家商品数据统计id 自赠'),
            'order' => 1,
        ),
        'shop_id' => array(
            'type' => 'table:shop@sysshop',
            'required' => true,
            'comment' => app::get('sysstat')->_('所属商家'),
        ),
        'item_id' => array(
            'type' => 'table:item@sysitem',
            'required' => true,
            'comment' => app::get('sysstat')->_('商品id'),
        ),
        'title' => array(
            //'type' => 'varchar(60)',
            'type' => 'string',
            'length' => 60,
            'required' => true,
            'default' => '',
            'comment' => app::get('sysstat')->_('商品标题'),
        ),
        'pic_path' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'comment' => app::get('sysstat')->_('商品图片绝对路径'),
        ),
        'amountnum' => array(
            'type' => 'number',
            'comment' => app::get('sysstat')->_('销售数量'),
        ),
        'amountprice' => array(
            'type' => 'money',
            'comment' => app::get('sysstat')->_('销售总价'),
        ),

        'createtime'=>array(
            'type' => 'time',
            'comment' => app::get('sysstat')->_('创建时间'),
        ),
    ),

    'primary' => 'item_stat_id',
);
