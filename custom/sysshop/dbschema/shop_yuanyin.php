<?php

/**
 * ShopEx LuckyMall
 *
 * @author     ajx
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return  array(
    'columns' => array(
        'shop_yuanyin_id'=>array(
            'type'=>'number',
            //'pkey'=>true,
            'autoincrement' => true,
            'required' => true,
            'label' => 'id',
            'comment' => app::get('sysshop')->_('企业注册原因Id'),
            'order' => 1,
        ),
        'shop_id'=>array(
            'type'=>'number',
            // 'required' => true,
            'label' => '企业id',
            'comment' => app::get('sysshop')->_('企业id'),
        ),
        'yuanyin' => array(
            'type' => 'string',
            'order' => 30,
            'width' => 100,
            'label' => app::get('sysshop')->_('yuanyin'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'modified_time' =>
        array (
            'type' => 'last_modify',
            'label' => app::get('sysshop')->_('最后修改时间'),
        ),
    ),

    'primary' => 'shop_yuanyin_id',
    'comment' => app::get('sysshop')->_('企业主要产品表'),
);

