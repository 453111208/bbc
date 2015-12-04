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
        'seller_id' =>
        array (
            'type' => 'table:account@sysshop',
            //'pkey' => true,
            'label' => app::get('sysshop')->_('企业账号ID'),
            'width' => 110,
            'order' => 10,
            'in_list' => true,
            'default_in_list' => true,
        ),

        'shop_id'=>array(
            'type'=>'number',
            // 'required' => true,
            'label' => '企业id',
            'comment' => app::get('sysshop')->_('企业id'),
        ),
        'seller_type' => array(
            'type' =>array(
                            '1'=>'回收处置企业',
                            '2'=>'产废企业',
                        ),
            'required' => true,
            'default' => '1',
            'order' => 30,
            'width' => 100,
            'label' => app::get('sysshop')->_('企业账号类型'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'green'=>array(
            'type'=>'string',
            'comment'=> app::get('sysshop')->_('绿色指数'),
            'default' => '0',
        ),
        'modified_time' =>
        array (
            'type' => 'last_modify',
            'label' => app::get('sysshop')->_('最后修改时间'),
        ),
    ),

    'primary' => 'seller_id',
    'comment' => app::get('sysshop')->_('企业绿色指数'),
);

