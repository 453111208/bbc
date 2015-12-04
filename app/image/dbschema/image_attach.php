<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array (
    'columns' =>
    array (
        'attach_id' => array (
            'type' => 'number',
            'required' => true,
            'editable' => false,
            //'pkey'=>true,
            'autoincrement' => true,
            'comment' => app::get('image')->_('图片关联表ID'),
        ),
        'target_id' => array (
            //'type' => 'bigint(20)',
            'type' => 'bigint',
            'unsigned' => true,
            
            'required' => true,
            'default' => 0,
            'editable' => false,
            'comment' => app::get('image')->_('对象id-外键'),//user_id, shop_id, account_id
        ),
        'target_type' => array (
            //'type' => 'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'required' => true,
            'default' => 0,
            'editable' => false,
            'comment' => app::get('image')->_('用户类型'),//seller user admin
        ),
        'image_id' => array (
            'type' => 'table:image',
            'required' => true,
            'default' => 0,
            'editable' => false,
            'comment' => app::get('image')->_('图片的主键-外键关联image表'),
        ),
        'last_modified'=>array(
            'label'=>app::get('image')->_('更新时间'),
            'type' => 'last_modify',
            'width'=>180,
            'required' => true,
            'default' => 0,
            'editable' => false,
            'in_list'=>true,
            'default_in_list'=>true,
        ),
    ),
    'primary' => 'attach_id',
    'index' => array(
        'index_1' => ['columns' => ['target_id', 'target_type']],
    ),
    'comment' => app::get('image')->_('图片关联表'),
);
