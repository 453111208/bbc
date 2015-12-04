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
        'id' =>
        array (
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'comment' => app::get('image')->_('ID'),
        ),
        'storage'=>array(
            'label'=>app::get('image')->_('存储引擎'),
            'type' => 'string',
            'length' => 50,
            'default' => 'filesystem',
            'required' => true,
            'in_list'=>true,
            'width'=>100,
            'default_in_list'=>true,
        ),
        'image_name'=>array(
            'label'=>app::get('image')->_('图片名称'),
            'type' => 'string',
            'in_list'=>true,
            'length' => 200,
            'required' => false,
            'width'=>100,
            'default_in_list'=>true,
        ),
        'target_id' => array (
            'type' => 'bigint',
            'unsigned' => true,
            'required' => true,
            'default' => 0,
            'editable' => false,
            'comment' => app::get('image')->_('关联ID'),//user_id, shop_id, account_id seller_id
        ),
        'target_type' => array (
            'type' => 'string',
            'length' => 20,
            'required' => true,
            'default' => '',
            'editable' => false,
            'comment' => app::get('image')->_('用户类型'),//shop user admin seller 如果店铺入住上传图片，在还没有店铺ID的时候，则存储为seller类型
        ),
        'img_type' => array(
            'type' => 'string',
            'required' => true,
            'default' => '',
            'comment' => app::get('image')->_('图片类型，根据图片类型生成不同大小的图片'),
        ),
        'url'=>array(
            'label'=>app::get('image')->_('网址'),
            //'type'=>'varchar(200)',
            'type' => 'string',
            'required' => true,
            'width'=>300,
            'in_list'=>false,
        ),
        'ident'=>array(
            //'type' => 'varchar(200)',
            'type' => 'string',
            'length' => 200,
            'comment' => app::get('image')->_('唯一标识'),
        ),
        'width'=>array(
            'label'=>app::get('image')->_('宽度'),
            'type' => 'number',
            'in_list'=>true,
            'default_in_list'=>false,
        ),
        'height'=>array(
            'label'=>app::get('image')->_('高度'),
            'type' => 'number',
            'in_list'=>true,
            'default_in_list'=>false,
        ),
        'size' => array(
            'type' => 'number',
            'in_list'=>true,
            'default_in_list'=>false,
            'label'=>app::get('image')->_('文件大小'),
        ),
        'last_modified' => array (
            'label'=>app::get('image')->_('更新时间'),
            'type' => 'last_modify',
            'width'=>180,
            'required' => true,
            'default' => 0,
            'editable' => false,
            'in_list'=>true,
            'default_in_list'=>true,
            'filtertype' => 'yes'
        ),
        'disabled' => array(
            'type' => 'bool',
            'default' => 0,
            'required' => true,
            'comment' => app::get('sysitem')->_('disabled'),
        ),
    ),
    'primary' => 'id',
    'index' => array(
        'ind_target' => [
            'columns' => ['target_id','target_type'],
         ],
        'ind_url' => [
            'columns' => ['url'],
            'prefix' => 'unique',
         ],
         'ind_unique' => [
            'columns' => ['url','target_id','target_type'],
            'prefix' => 'unique',
        ],
    ),
    'comment' => app::get('image')->_('图片表'),
);
