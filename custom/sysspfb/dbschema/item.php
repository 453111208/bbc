<?php
return  array(
    'columns'=> array(
        'item_id' => array(
            'type' => 'number',
            //'pkey' => true,
            'autoincrement' => true,
            'required' => true,
            'comment' => app::get('sysspfb')->_('item_id'),
        ),
           'title' => array(
            //'type' => 'varchar(60)',
            'type' => 'string',
            'length' => 90,
            'required' => true,
            'default' => '',
             'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysspfb')->_('商品名称'),
            'comment' => app::get('sysspfb')->_('商品名称'),
            'is_title' => true,
            'searchtype' => 'has',
            'filtertype' => 'custom',
            'filterdefault' => true,
            'order' => 11,
        ),
              'sub_title' => array(
            //'type' => 'varchar(200)',
            'type' => 'string',
            'length' => 200,
             'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysspfb')->_('商品子标题'),
            'comment' => app::get('sysspfb')->_('商品子标题'),
            'filtertype' => 'normal',
            'filterdefault' => true,
             'order' => 12,
        ),
        'user_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => app::get('sysspfb')->_('所属会员'),
            'comment' => app::get('sysspfb')->_('会员ID'),
            'in_list' => true,
            'order' => 16,
            //'orderby' => true,
        ),
        'cat_id' => array(
            'type' => 'number',
            'required' => true,
            'label' => app::get('sysspfb')->_('商品类目名称'),
            'comment' => app::get('sysspfb')->_('商品类目ID'),
            'finder_filter_name'=>'cat_name',//后台高级筛使用
            'in_list' => true,
            'filtertype' => 'yes',
            'filterdefault' => true,
            'order' => 13,
        ),
        'shop_cat_id' => array(
            //'type' => 'varchar(255)',
            'type' => 'string',
            'label' => app::get('sysspfb')->_('企业自定义分类id'),
            'comment' => app::get('sysspfb')->_('企业自定义分类id'),
        ),
     
     
         'state' => array(
            //'type' => 'varchar(200)',
            'type' => 'bool',
            'length' => 20,
            'label' => app::get('sysspfb')->_('平台方审核状态'),
            'comment' => app::get('sysspfb')->_('平台方审核状态'),
             'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'normal',
            'filterdefault' => true,
              'order' => 14,
        ),
            'otherstate' => array(
            //'type' => 'varchar(200)',
            'type' => 'bool',
            'length' => 20,
            'label' => app::get('sysspfb')->_('第三方审核状态'),
            'comment' => app::get('sysspfb')->_('第三方审核状态'),
             'in_list' => true,
            'default_in_list' => true,
            'filtertype' => 'normal',
            'filterdefault' => true,
              'order' => 15,
        ),


        'modified_time' => array(
            'type' => 'last_modify',
            'required' => true,
            'label' => app::get('sysspfb')->_('更新时间'),
            'comment' => app::get('sysspfb')->_('商品最后更新时间'),
            'in_list' => true,
            'default_in_list' => false,
            'order' => 17,
        ),
        'item_numberro' => array(
            'type' => 'text',
            
            'label' => app::get('sysspfb')->_('商品介绍'),
        ),

    ),
    'primary' => 'item_id',
    'comment' => app::get('sysspfb')->_('商品表'),
);

