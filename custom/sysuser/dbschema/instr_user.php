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
        'instr_id' =>
        array (
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'editable' => false,
            'label' => app::get('sysuser')->_('兴趣ID'),
        ),
        'user_id' =>
        array (
            'type' => 'number',
            'required' => true,
            'comment' => app::get('sysuser')->_('用户ID'),
        ),
        'instr_cat' =>
        array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'label' => app::get('sysuser')->_('兴趣类型'),
            'width' => 75,
            'comment' => app::get('sysuser')->_('兴趣子类型多个用逗号分隔'),
        ),
         'instr_catparent' =>
        array (
            //'type' => 'varchar(50)',
             'type' => array(
                '1' => app::get('sysuser')->_('行情'),
                '2' => app::get('sysuser')->_('资讯'),
                '3' => app::get('sysuser')->_('供求'),
                '4' => app::get('sysuser')->_('交易'),
                '5' => app::get('sysuser')->_('名人专家'),
            ),
            'length' => 50,
            'required' => true,
            'label' => app::get('sysuser')->_('兴趣大类'),
            'width' => 75,
        ),
         'order_sort'=> array (
             'type' => array(
                '1' => app::get('sysuser')->_('左上'),
                '2' => app::get('sysuser')->_('右上'),
                '3' => app::get('sysuser')->_('左下'),
                '4' => app::get('sysuser')->_('右下'),
            ),
            'required' => true,
            'default' => 1,
            'editable' => true,
            'comment' => app::get('sysuser')->_('排序'),
        ),
      
    ),

    'primary' => 'instr_id',
    'comment' => app::get('sysuser')->_('会员兴趣表'),
);
