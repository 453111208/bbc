<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return  array(
    'columns' => array(
        'cart_id' => array(
            'type' => 'number',
            //'pkey' => true,
            'autoincrement' => true,
            'required' => true,
        ),
        'user_ident' => array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'required' => true,
            'comment' => app::get('systrade')->_('会员ident,会员信息和session生成的唯一值'),
        ),
        'user_id' => array(
            //'type' => 'int(8) ',
            'type' => 'integer',
            //'pkey' => true,
            'required' => true,
            'default' => -1,
            'comment' => app::get('systrade')->_('会员id'),
            'label' => app::get('systrade')->_('会员id'),
        ),
        'shop_id'=> array(
            'type'=>'number',
            'required' => true,
            'comment' => app::get('systrade')->_('店铺ID'),
            'label' => app::get('systrade')->_('店铺ID'),
        ),
        'obj_type' => array(
            //'type' => 'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'required' => true,
            'comment' => app::get('systrade')->_('购物车对象类型'),
            'label' => app::get('systrade')->_('购物车对象类型'),
        ),
        'item_id' => array(
            'type' => 'number',
            'required' => true,
            'comment' => app::get('systrade')->_('商品id'),
            'label' => app::get('systrade')->_('商品id'),
        ),
        'sku_id' => array(
            'type' => 'number',
            'required' => true,
            'comment' => app::get('systrade')->_('sku的id'),
            'label' => app::get('systrade')->_('sku的id'),
        ),
        'title' => array(
            //'type' => 'varchar(60)',
            'type' => 'string',
            'length' => 60,
            'required' => true,
            'default' => '',
            'comment' => app::get('systrade')->_('商品标题'),
            'label' => app::get('systrade')->_('商品标题'),
        ),
        'image_default_id' => array(
            //'type' => 'varchar(32)',
            'type' => 'string',
            'comment' => app::get('systrade')->_('商品默认图'),
            'label' => app::get('systrade')->_('商品默认图'),
        ),
        'quantity' => array(
            'type' => 'float',
            'unsigned' => true,
            'required' => true,
            'comment' => app::get('systrade')->_('数量'),
            'label' => app::get('systrade')->_('数量'),
        ),
        'is_checked' => array(
            'type' => 'bool',
            'default' => '0',
            'required' => true,
            'comment' => app::get('systrade')->_('是否购物车选中'),
            'label' => app::get('systrade')->_('是否购物车选中'),
        ),
        'selected_promotion' => array(
            //'type' => 'varchar(30)',
            'type' => 'string',
            'length' => 30,
            'default' => '',
            'required' => true,
            'comment' => app::get('systrade')->_('购物车选中的促销ID'),
            'label' => app::get('systrade')->_('购物车选中的促销ID'),
        ),
        'created_time' => array(
            'type' => 'time',
            'comment' => app::get('systrade')->_('加入购物车时间'),
            'label' => app::get('systrade')->_('加入购物车时间'),
        ),
        'modified_time' => array(
            'type' => 'time',
            'comment' => app::get('systrade')->_('最后修改时间'),
            'label' => app::get('systrade')->_('最后修改时间'),
        ),
    ),
    'primary' => ['cart_id', 'user_id'],
    'index' => array(
        'ind_sku_id' => ['columns' => ['sku_id', 'user_ident']],
        'ind_shop_id' => ['columns' => ['shop_id']],
        'ind_user_id' => ['columns' => ['user_id']],
    ),
    'unbackup' => true,
    'comment' => app::get('systrade')->_('购物车'),
);
