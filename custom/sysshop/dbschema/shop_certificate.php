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
        'certificate_id'=>array(
            'type'=>'number',
            //'pkey'=>true,
            'autoincrement' => true,
            'required' => true,
            'label' => 'id',
            'comment' => app::get('sysshop')->_('资质ID'),
            'order' => 1,
        ),
        'shop_id'=>array(
            'type'=>'number',
            // 'required' => true,
            'label' => '企业id',
            'comment' => app::get('sysshop')->_('企业id'),
        ),
        'certificate' => array(
            'type' => 'string',
            'order' => 30,
            'width' => 100,
            'label' => app::get('sysshop')->_('资质名称'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'certificate_img'=>array(
            'type'=>'string',
            'comment'=> app::get('sysshop')->_('资质副本'),
        ),
        'modified_time' =>
        array (
            'type' => 'last_modify',
            'label' => app::get('sysshop')->_('最后修改时间'),
        ),
    ),

    'primary' => 'certificate_id',
    'comment' => app::get('sysshop')->_('企业资质表'),
);

