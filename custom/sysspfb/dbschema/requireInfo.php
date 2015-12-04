<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array(
    'columns' => array(
         'require_id' => array(
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'comment' => app::get('sysspfb')->_('求购信息ID'),
            'width' => 50,
        ),
             'number' => array(
            'type' => 'string',
            'comment' => app::get('sysspfb')->_('编号'),
            'length' => 110,
            //    'in_list' => true,
            // 'default_in_list' => true,
        ),
        'cat_id' => array(
            'type' => 'string',
            'comment' => app::get('sysspfb')->_('分类ID'),
            'length' => 110,
        ),
         'cat_name' => array(
            'type' => 'string',
            'comment' => app::get('sysspfb')->_('分类名称'),
             'label' => app::get('sysspfb')->_('分类名称'),
            'length' => 110,
               'in_list' => true,
            'default_in_list' => true,
        ),
         'variety_name' => array(
            'type' => 'string',
            'length' => 100,
            'default' => '',
            'label' => app::get('sysspfb')->_('品名'),
               'in_list' => true,
            'default_in_list' => true,
            
        ),
         'effective_time' => array(
            'type' => 'string',
            'length' => 100,
            'label' => app::get('sysspfb')->_('有效时间'),
               'in_list' => true,
            'default_in_list' => true,
          
        ),
          'price_method' => array(
            'type' => array(
                'gongkai' => app::get('sysspfb')->_('价格公开'),
                'mianyi' => app::get('sysspfb')->_('价格面议'),
            ),
            'length' => 100,
            'label' => app::get('sysspfb')->_('价格方式'),
            'in_list' => true,
            'default_in_list' => true,
        ),

         'product_intro' => array(
            'type' => 'text',
            'label' => app::get('sysspfb')->_('信息介绍'),

        ),
        'name' => array(
            'type' => 'string',
            'length' => 50,
            'label' => app::get('sysspfb')->_('联系人'),
        ),
          'tel' => array(
            'type' => 'string',
            'length' => 50,
            'label' => app::get('sysspfb')->_('电话'),
        ),
           'email' => array(
            'type' => 'string',
            'length' => 100,
            'label' => app::get('sysspfb')->_('邮箱'),
        ),
            'create_time' =>array(
            'label' => app::get('sysspfb')->_('发布日期'),
            'width' => 150,
            'type' => 'time',
            'editable' => false,
            'filtertype' => 'time',
            'filterdefault' => true,
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysspfb')->_('发布日期'),
               'in_list' => true,
            'default_in_list' => true,
        ),
            'approve_stats' => array(
            'type' => 'bool',
            'label' => app::get('sysspfb')->_('审核状态'),

            'default' => 0,
        ),
         'show_stats' => array(
            'type' => 'bool',
            'label' => app::get('sysspfb')->_('是否上架'),
            'default' => 0,

        ),
            'norms'=> array(
            'type' => 'string',
            'length' => 50,
            'label' => app::get('sysspfb')->_('规格规范'),
        ),
            'tag'=> array(
             'type' => 'string',
            'length' => 50,
            'label' => app::get('sysspfb')->_('关键字'),
        ),
           'user_id' =>array(
            'type' => 'number',
            'width' => 100,
            'label' => app::get('sysspfb')->_('会员ID'),

        ),
        'item_id' => array(
            'type' => 'string',
            'length' => 100,
            'label' => app::get('sysspfb')->_('商品ID'),
        ),
        'price' => array(
            'type' => 'string',
            'length' => 100,
            'label' => app::get('sysspfb')->_('价格'),
        ),
        'countnum' => array(
            'type' => 'number',
            'length' => 100,
            'label' => app::get('sysspfb')->_('数量'),
        ),
        'company' => array(
            'type' => 'string',
            'length' => 100,
            'label' => app::get('sysspfb')->_('单位'),
        ),
        'image_default_id' => array(
            //'type' => 'varchar(32)',
            'type' => 'string',
            // 'required' => true,
            'comment' => app::get('sysspfb')->_('商品默认图'),
        ),
        'list_image' => array(
            'type' => 'text',
            // 'required' => true,
            'comment' => app::get('sysspfb')->_('商品图片'),
        ),
    ),
    'primary' => 'require_id',
    'comment' => app::get('sysspfb')->_('求购信息发布表'),
);
