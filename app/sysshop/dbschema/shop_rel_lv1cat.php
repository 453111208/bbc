<?php
return array(
    'columns'=>array(
        'rel_id'=>array(
            'type'=>'number',
            //'pkey'=>true,
            'autoincrement' => true,
            'required' => true,
            'label' => 'id',
            'comment' => app::get('sysshop')->_('店铺类目关联'),
            'order' => 1,
        ),
        'cat_id'=>array(
            'type'=>'table:cat@syscategory',
            // 'pkey'=>true,
            'required' => true,
            'comment' => app::get('sysshop')->_(' 关联类目id'),
            'label' => app::get('sysshop')->_('类目名称'),
            'order' => 2,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'shop_id'=>array(
            'type'=>'table:shop',
            //'pkey'=>true,
            'required' => true,
            'comment' => app::get('sysshop')->_('关联店铺id'),
            'label' => app::get('sysshop')->_('店铺名称'),
            'order' => 3,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'fee_confg'=>array(
          'type' => 'serialize',
          'default' => '0',
          'label' => app::get('sysshop')->_('商家类目金配置'),
          'width' => 75,
          //'in_list' => fasle,
        ),
    ),
    'primary' => 'rel_id',
    'index' => array(
        'ind_unique' => [
            'columns' => ['cat_id', 'shop_id'],
            'prefix' => 'unique',
        ],
    ),
    'comment'=>app::get('sysshop')->_('店铺关联类目表'),
);


