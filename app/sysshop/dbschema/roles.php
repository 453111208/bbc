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
        'role_id' =>
        array (
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'comment' => app::get('sysshop')->_('角色ID'),
        ),
        'shop_id'=>array(
            'type'=>'table:shop',
            'required' => true,
            'comment' => app::get('sysshop')->_('店铺ID'),
            'label' => app::get('sysshop')->_('店铺名称'),
            'in_list' => true,
            'default_in_list'=>true,
        ),
        'role_name' =>
        array (
            'type' => 'string',
            'length' => 100,
            'required' => true,
            'label' => app::get('sysshop')->_('角色名'),
            'width' => 310,
            'in_list' => true,
            'is_title' => true,
            'default_in_list' => true,
        ),
        'workground' =>
        array (
            'label' => app::get('sysshop')->_('权限ID'),
            'type' => 'text',
        ),
    ),

    'primary' => 'role_id',
    'comment' => app::get('sysshop')->_('角色表'),
);
