<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2015 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 
// 免邮规则表
return  array(
    'columns' => array(
        'freepostage_id' => array(
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'width' => 110,
            'label' => app::get('syspromotion')->_('id'),
            'comment' => app::get('syspromotion')->_('免邮方案id'),
        ),
        'shop_id' => array(
            'type' => 'number',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'width' => 100,
            'label' => app::get('syspromotion')->_('所属商家'),
            'comment' => app::get('syspromotion')->_('所属商家的店铺id'),
        ),
        'freepostage_name' => array(
            'type' => 'string',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'width' => 110,
            'label' => app::get('syspromotion')->_('免邮名称'),
            'comment' => app::get('syspromotion')->_('免邮名称'),
        ),
        'freepostage_desc' => array(
            'type' => 'string',
            'required' => true,
            'in_list' => true,
            'width' => 110,
            'label' => app::get('syspromotion')->_('免邮描述'),
            'comment' => app::get('syspromotion')->_('免邮描述'),
        ),
        'used_platform' => array(
            'type' => array(
                '0' => app::get('syspromotion')->_('商家全场可用'),
                '1' => app::get('syspromotion')->_('只能用于pc'),
                '2' => app::get('syspromotion')->_('只能用于wap'),
            ),
            'default' => 0,
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('syspromotion')->_('使用平台'),
            'comment' => app::get('syspromotion')->_('使用平台'),
        ),
        'valid_grade' => array(
            'type' => 'string',
            'default' => '',
            'required' => true,
            'label' => app::get('syspromotion')->_('会员级别集合'),
        ),
        'gentype' => array(
            'type' => array(
                '0' => app::get('syspromotion')->_('独立添加'),
                '1' => app::get('syspromotion')->_('绑定促销'),
            ),
            'default' => '0',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '50',
            'label' => app::get('syspromotion')->_('生成类型'),
            'comment' => app::get('syspromotion')->_('生成类型'),
        ),
        'condition_type' => array(
            'type' => array(
                'money' => app::get('syspromotion')->_('按金额'),
                'quantity' => app::get('syspromotion')->_('按数量'),
            ),
            'default' => 'money',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '50',
            'label' => app::get('syspromotion')->_('免邮条件标准'),
            'comment' => app::get('syspromotion')->_('免邮条件标准'),
        ),
        'limit_money' => array(
            'type' => 'money',
            'default' => '0',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '50',
            'label' => app::get('syspromotion')->_('满足条件金额'),
            'comment' => app::get('syspromotion')->_('满足条件金额'),
        ),
        'limit_quantity' => array(
            'type' => 'number',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '50',
            'label' => app::get('syspromotion')->_('满足条件数量'),
            'comment' => app::get('syspromotion')->_('满足条件数量'),
        ),
        'use_bound' => array(
            'type' => array(
                '0' => app::get('syspromotion')->_('商家全场可用'),
                '1' => app::get('syspromotion')->_('指定商品可用'),
            ),
            'default' => '0',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '50',
            'label' => app::get('syspromotion')->_('使用范围'),
            'comment' => app::get('syspromotion')->_('使用范围'),
        ),
        'start_time' => array(
            'type' => 'time',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '100',
            'label' => app::get('syspromotion')->_('免邮生效时间'),
            'comment' => app::get('syspromotion')->_('免邮生效时间'),
        ),
        'end_time' => array(
            'type' => 'time',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '100',
            'label' => app::get('syspromotion')->_('免邮失效时间'),
            'comment' => app::get('syspromotion')->_('免邮失效时间'),
        ),
        'created_time' => array(
            'type' => 'time',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '100',
            'label' => app::get('syspromotion')->_('创建时间'),
            'comment' => app::get('syspromotion')->_('创建时间'),
        ),
        'promotion_tag' => array(
            'type' => 'string',
            'length' => 15,
            'required' => true,
            'label' => app::get('syspromotion')->_('促销标签'),
            'comment' => app::get('syspromotion')->_('促销标签'),
        ),
        'freepostage_status' => array(
            'type' => array(
                'pending' => app::get('syspromotion')->_('待审核'),
                'agree' => app::get('syspromotion')->_('审核通过'),
                'refuse' => app::get('syspromotion')->_('审核拒绝'),
                'cancel' => app::get('syspromotion')->_('已取消'),
            ),
            'default' => 'agree',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'width' => 110,
            'label' => app::get('syspromotion')->_('促销状态'),
            'comment' => app::get('syspromotion')->_('促销状态'),
        ),
    ),
    
    'primary' => 'freepostage_id',
    'comment' => app::get('syspromotion')->_('免邮表'),
);
