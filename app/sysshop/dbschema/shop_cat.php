<?php

/**
 * ShopEx LuckyMall
 *
 * @author     ajx
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array(
    'columns' => array(
        'cat_id' => array(
            'type'=>'number', 
            'label' => 'id',
            'comment' => app::get('sysshop')->_('分类id'),
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'order' => 1,
        ),
        'shop_id' => array(
            'type'=>'table:shop',
            'label' => app::get('sysshop')->_('店铺名称'),
            'required' => true,
            'in_list' => true,
            'default_in_list'=>true,
        ),
        'parent_id' => array(
            'type'=>'number',
            'comment' => app::get('sysshop')->_('父类ID'),
            'required' => true,
            'default' => 0,
        ),
        'cat_path' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'default' => ',',
            'label' => app::get('sysshop')->_('分类路径(从根至本结点的路径,逗号分隔,首部有逗号)'),
            'width' => 110,
            'in_list' => true,
        ),
        'level' => array(
            'type' => array(
                '1' => app::get('sysshop')->_('一级分类'),
                '2' => app::get('sysshop')->_('二级分类'),
            ),
            'default' => '1',
            'label' => app::get('sysshop')->_('分类层级'),
            'width' => 110,
        ),
        'is_leaf' => array(
            'type' => 'bool',
            'required' => true,
            'default' => 0,
            'label' => app::get('sysshop')->_('是否叶子结点（true：是；false：否）'),
            'width' => 110,
            'in_list' => true,
        ),
        'cat_name' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'required' => true,
            'is_title' => true,
            'default' => '',
            'label' => app::get('sysshop')->_('分类名称'),
            'width' => 110,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'order_sort' => array(
            'type' => 'number',
            'label' => app::get('sysshop')->_('排序'),
            'width' => 110,
            'default' => 0,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'modified_time' => array(
            'type' => 'last_modify',
            'label' => app::get('sysshop')->_('更新时间'),
            'width' => 110,
            'in_list' => true,
            'orderby' => true,
        ),
        'disabled' => array(
            'type' => 'bool',
            'default' => 0,
            'required' => true,
            'label' => app::get('sysshop')->_('是否屏蔽（true：是；false：否）'),
            'width' => 110,
            'in_list' => true,
        ),
    ),
    'primary' => 'cat_id',
    'index' => array(
        'ind_parent_id' => ['columns' => ['parent_id']],
        'ind_cat_shop_id' => ['columns' => ['shop_id', 'cat_id']],
    ),
    'comment' => app::get('sysshop')->_('店铺分类表'),
);

