<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return  array(
    'columns' => array(
        'tid' => array(
            //'type' => 'bigint unsigned',
            'type' => 'bigint',
            'unsigned' => true,
            
            //'pkey' => true,
            'required' => true,
            'comment' => app::get('sysrate')->_('订单ID'),
        ),
        'cat_id'=>array(
            'type'=>'number',
            'required' => true,
            'comment' => app::get('sysrate')->_(' 关联类目id'),
        ),
        'user_id' => array(
            //'type' => 'bigint unsigned',
            'type' => 'bigint',
            'unsigned' => true,
            
            'required' => true,
            'comment' => app::get('sysrate')->_('用户ID'),
        ),
        'shop_id' => array(
            //'type' => 'bigint unsigned',
            'type' => 'bigint',
            'unsigned' => true,
            
            'required' => true,
            'comment' => app::get('sysrate')->_('店铺ID'),
        ),
        'tally_score' => array(
            //'type' => 'int',
            'type' => 'smallint',
            
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysrate')->_('宝贝与描述相符'),
        ),
        'attitude_score' => array(
            'type' => 'smallint',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysrate')->_('服务态度评分'),
        ),
        'delivery_speed_score' => array(
            'type' => 'smallint',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysrate')->_('发货速度评分'),
        ),
        'logistics_service_score' => array(
            'type' => 'smallint',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysrate')->_('物流公司服务评分'),
        ),
        'created_time' =>
        array(
            'type' => 'time',
            'label' => app::get('sysrate')->_('创建时间'),
            'width' => '100',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'modified_time' => array(
            'type' => 'last_modify',
            'label' => app::get('sysrate')->_('最后修改时间'),
            'in_list' => true,
            'default_in_list' => true,
            'width' => '100',
        ),
        'disabled' => array(
            'type' => 'bool',
            'default' => 0,
            'required' => true,
            'editable' => false,
            'comment' => app::get('sysrate')->_('是否有效'),
        ),
    ),
    
    'primary' => 'tid',
    'comment' => app::get('sysrate')->_('店铺评分表'),
);

