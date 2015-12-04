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
        'literarytarget_id' =>array (
            'type' => 'number',
            'required' => true,
            'comment'=> app::get('sysplan')->_('成功案例案例目标ID'),
            'autoincrement' => true,   //序号自增
            'width' => 50,
            'order'=>1,
            ),
        
        
        'literarytarget' => array(
            'type' => 'string',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysplan')->_('案例目标'),
            'comment' => app::get('sysplan')->_('案例目标'),
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
    'primary' => 'literarytarget_id',
    'comment' => app::get('sysplan')->_('成功案例案例目标表'),
);
