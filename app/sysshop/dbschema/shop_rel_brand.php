<?php
return array(
    'columns'=>array(
        'shop_id'=>array(
            'type'=>'table:shop',
            //'pkey'=>true,
            'required' => true,
            'comment' => app::get('sysshop')->_(' 关联店铺id'),
        ),
        'brand_id'=>array(
            'type'=>'table:brand@syscategory',
            //'pkey'=>true,
            'required' => true,
            'comment' => app::get('sysshop')->_(' 关联品牌id'),
        ),
        'brand_warranty'=>array(
            //'type'=>'varchar(50)',
            'type' => 'string',
            'comment' => app::get('sysshop')->_('品牌授权书'),
        ),
    ),

    'primary' => ['shop_id', 'brand_id'],
);

