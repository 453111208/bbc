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
        'literaryclass_id' =>array (
            'type' => 'number',
            'required' => true,
            'comment'=> app::get('sysplan')->_('成功案例案例分类ID'),
            'autoincrement' => true,   //序号自增
            'width' => 50,
            'order'=>1,
            ),
        
        
        'literaryclass' => array(
            'type' => 'string',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysplan')->_('案例分类'),
            'comment' => app::get('sysplan')->_('案例分类'),
            'order'=>5,
        ),
        'order_sort'=> array (
            'type' => 'number',
            'required' => true,
            'default' => 0,  
            'editable' => true,
            'comment' => app::get('sysplan')->_('排序'),
        ),
        'modified'=> array (
            'type' => 'time',
            'editable' => true,
            'in_list' => true,
            'label' => app::get('sysplan')->_('修改时间'),
            'comment' => app::get('sysplan')->_('修改时间'),
        ),


       

  ),
    'primary' => 'literaryclass_id',
    'comment' => app::get('sysplan')->_('成功案例案例分类表'),
);
