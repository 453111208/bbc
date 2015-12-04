<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return  array(
    'columns' => array(
        'prop_id' => array(
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'comment' => app::get('syscategory')->_('属性id'),
        ),
        'prop_name' => array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'required' => true,
            'default' => '',
            'order' => 10,
            'label' => app::get('syscategory')->_('属性名称'),
            'width' => 180,
            'editable' => true,
            'in_list' => true,
            'is_title' => true,
            'default_in_list' => true,
        ),
        'type'=>array(
            //'type'=>'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'label' => app::get('syscategory')->_('展示类型')
        ),
        'search'=>array(
            //'type'=>'varchar(20)',
            'type' => 'string',
            'length' => 20,
            'required' => true,
            'label' =>app::get('syscategory')->_('搜索方式'),
            'default' => 'select'
        ),
        'show' => array(
            //'type' => 'varchar(10)',
            'type' => 'string',
            'length' => 10,
            
            'required' => true,
            'default' => '',
            'in_list' => true,
            'label' => app::get('syscategory')->_('是否显示'),
            'comment' => app::get('syscategory')->_('是否显示'),
        ),
        'is_def' => array(
          'type' => 'bool',
          'default' => 0,
          'required' => true,
          'label' => app::get('syscategory')->_('是否系统默认属性'),
          'width' => 110,
          'editable' => false,
          'in_list' => true,
          'comment' => app::get('syscategory')->_('是否系统默认属性'),
        ),
        'show_type' => array(
            'type' => array(
                'text' => app::get('syscategory')->_('文字'),
                'image' => app::get('syscategory')->_('图片'),
            ),
            'default' => 'text',
            'order' => 30,
            'required' => true,
            'label' => app::get('syscategory')->_('类型'),
            'width' => 75,
            'editable' => false,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'prop_type' => array(
            'type' => array(
                'spec' => app::get('syscategory')->_('销售属性'),
                'nature' => app::get('syscategory')->_('自然属性'),
            ),
            'default' => 'spec',
            'order' => 30,
            'required' => true,
            'label' => app::get('syscategory')->_('属性类型'),
            'width' => 75,
            'editable' => false,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'prop_memo' => array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'default' => '',
            'order' => 40,
            'required' => true,
            'label' => app::get('syscategory')->_('属性备注'),
            'width' => 100,
            'editable' => false,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'order_sort' => array(
            'type' => 'number',
            'default' => 1,
            'order' => 50,
            'required' => true,
            'editable' => false,
            'deny_export' => true,
            'label' => app::get('syscategory')->_('排序'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'modified_time' => array(
            'type' => 'last_modify',
            'label' => app::get('syscategory')->_('更新时间'),
            'width' => 110,
            'order' => 50,
            'editable' => false,
            'in_list' => true,
            'default_in_list' => false,
        ),
        'disabled' => array(
            'type' => 'bool',
            'default' => 0,
            'required' => true,
            'editable' => false,
            'deny_export' => true
        ),
    ),
    
    'primary' => 'prop_id',
    'comment' => app::get('syscategory')->_('属性表'),
);
