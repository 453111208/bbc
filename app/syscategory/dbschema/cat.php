<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array(
    'columns' => array(
        'cat_id' => array(
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'comment' => app::get('syscategory')->_('分类ID'),
            'width' => 110,
        ),
        'parent_id' => array(
            'type' => 'number',
            'comment' => app::get('syscategory')->_('分类父级ID'),
            'width' => 110,
        ),
        'cat_name' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'required' => true,
            'is_title' => true,
            'default' => '',
            'label' => app::get('syscategory')->_('分类名称'),
            'width' => 110,
            'searchtype' => 'has',
            'in_list' => true,
            'default_in_list' => true,
        ),
        'cat_logo' => array(
            'type' => 'string',
            'comment' => app::get('syscategory')->_('一级分类logo'),
        ),
        'cat_path' => array(
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            'default' => ',',
            'comment' => app::get('syscategory')->_('分类路径(从根至本结点的路径,逗号分隔,首部有逗号)'),
            'width' => 110,
        ),
        'level' => array(
            'type' => array(
                '1' => app::get('syscategory')->_('一级分类'),
                '2' => app::get('syscategory')->_('二级分类'),
                '3' => app::get('syscategory')->_('三级分类'),
            ),
            'default' => '1',
            'label' => app::get('syscategory')->_('分类层级'),
            'width' => 110,
            'in_list' => true,
            'default_in_list' => false,
        ),
        'is_leaf' => array(
            'type' => 'bool',
            'required' => true,
            'default' => 0,
            'comment' => app::get('syscategory')->_('是否叶子结点（true：是；false：否）'),
            'width' => 110,
        ),
        'disabled' => array(
            'type' => 'bool',
            'default' => 0,
            'required' => true,
            'comment' => app::get('syscategory')->_('是否屏蔽（true：是；false：否）'),
            'width' => 110,
        ),
                /*
        'goods_count' => array(
            'type' => 'number',
            'label' => app::get('syscategory')->_('商品数'),
            'width' => 110,
        ),
                 */
        'addon' => array(
            'type' => 'text',
            'editable' => false,
            'comment' => app::get('syscategory')->_('附加项'),
        ),
        'child_count' => array(
            'type' => 'number',
            'default' => 0,
            'required' => true,
            'editable' => false,
            'comment' => app::get('syscategory')->_('子类别数量'),
        ),
        'params' => array(
            'type' => 'serialize',
            'editable' => false,
            'comment' => app::get('syscategory')->_('参数表结构(序列化) array(参数组名=>array(参数名1=>别名1|别名2,参数名2=>别名1|别名2))'),
        ),
        'guarantee_money' => array (
            'type' => 'money',
            'default' => '0',
            'label' => app::get('syscategory')->_('保证金'),
            'width' => 75,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'platform_fee' => array (
            'type' => 'money',
            'default' => '0',
            'label' => app::get('syscategory')->_('平台使用费'),
            'width' => 75,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'cat_service_rates' => array (
            'type' => 'money',
            'default' => '0',
            'label' => app::get('syscategory')->_('类目服务费率'),
            'width' => 75,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'order_sort' => array(
            'type' => 'number',
            'label' => app::get('syscategory')->_('排序'),
            'width' => 110,
            'default' => 0,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'modified_time' => array(
            'type' => 'last_modify',
            'label' => app::get('syscategory')->_('更新时间'),
            'width' => 110,
            'in_list' => true,
            'orderby' => true,
        ),
        'cat_template' => array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'comment' => app::get('syscategory')->_('类目对应的模板'),
        ),
    ),
    'primary' => 'cat_id',
    'comment' => app::get('syscategory')->_('类别属性值有限表'),
);
