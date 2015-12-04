<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return  array(
    'columns' => array(
        'shop_id' => array(
            //'type' => 'bigint unsigned',
            'type' => 'bigint',
            'unsigned' => true,
            //'pkey' => true,
            'required' => true,
            'comment' => app::get('sysrate')->_('店铺ID'),
        ),
        'cat_id'=>array(
            'type'=>'number',
            'required' => true,
            'comment' => app::get('sysrate')->_(' 关联类目id'),
        ),
        'tally_dsr' => array(
            'type' => 'serialize',
            'default' => '5',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysrate')->_('宝贝与描述相符'),
        ),
        'attitude_dsr' => array(
            'type' => 'serialize',
            'default' => '5',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysrate')->_('服务态度评分'),
        ),
        'delivery_speed_dsr' => array(
            'type' => 'serialize',
            'default' => '5',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysrate')->_('发货速度评分'),
        ),
        'modified_time' => array(
            'type' => 'last_modify',
            'label' => app::get('sysrate')->_('最后修改时间'),
            'in_list' => true,
            'default_in_list' => true,
            'width' => '100',
        ),
    ),
    
    'primary' => 'shop_id',
    'comment' => app::get('sysrate')->_('店铺动态评分统计表'),
);

