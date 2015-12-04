<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array(
    'columns' => array(
        'cat_id' => array(
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'comment' => app::get('sysspfb')->_('分类ID'),
            'width' => 110,
        ),
        'variety_id' => array(
            'type' => 'number',
            'comment' => app::get('sysspfb')->_('品名ID'),
            'width' => 110,
        ),
         'variety_name' => array(
            'type' => 'string',
            'length' => 100,
            'default' => '',
            'comment' => app::get('sysspfb')->_('品名'),
            'width' => 110,
        ),
        'cat_name' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'default' => '',
            'comment' => app::get('sysspfb')->_('分类名称'),
            'width' => 110,
        ),
         'effective_time' => array(
            'type' => 'string',
            'comment' => app::get('sysspfb')->_('有效时间'),
            'width' => 110,
        ),
         'price_method' => array(
            'type' => 'string',
            'comment' => app::get('sysspfb')->_('价格方式'),
            'width' => 110,
        ),
         'product_intro' => array(
            'type' => 'text',
            'editable' => false,
            'comment' => app::get('sysspfb')->_('产品介绍'),
        ),
   
         'name' => array(
            'type' => 'string',
            'length' => 50,
            'width' => 75,
             'comment' => app::get('sysspfb')->_('联系人'),
        ),
          'tel' => array(
            'type' => 'string',
            'length' => 50,
            'editable' => false,
            'comment' => app::get('sysspfb')->_('电话'),
        ),
           'email' => array(
            'type' => 'string',
            'length' => 100,
            'comment' => app::get('sysspfb')->_('邮箱'),
        ),
           'user_id' =>array(
            'type' => 'table:account@sysuser',
            //'pkey' => true,
            'required' => true,
            'comment' => app::get('sysspfb')->_('会员用户名'),
        ),
        'item_id' => array(
            'type' => 'table:item@sysitem',
            'required' => true,
            'label' => app::get('sysspfb')->_('关联商品'),
            'in_list' => true
            'comment' => app::get('sysspfb')->_('商品ID'),,
        ),
    ),
    'primary' => 'cat_id',
    'comment' => app::get('sysspfb')->_('供求信息发布表'),
);
