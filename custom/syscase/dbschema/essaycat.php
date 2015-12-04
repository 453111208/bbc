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
        'essaycat_id' =>array (
            'type' => 'number',
            'required' => true,
            'comment'=> app::get('syscase')->_('解决方案文章类型ID'),
            'autoincrement' => true,   //序号自增
            'width' => 50,
            'order'=>1,
          ),
         'essaycat' => array(
            'type' => 'string',
            'required' => true,
            'in_list' => true,  //显示在列表项中
            'default_in_list' => true,  //默认显示在列表项中
            'label' => app::get('syscase')->_('类型'),
            'comment' => app::get('syscase')->_('类型'),
            'order'=>2,
        ),
         'order_sort'=> array (
            'type' => 'number',
            'required' => true,
            'default' => 0,  
            'editable' => true,
            'comment' => app::get('syscase')->_('排序'),
        ),
        'modified'=> array (
            'type' => 'time',
            'editable' => true,
            'in_list' => true,
            'label' => app::get('syscase')->_('修改时间'),
            'comment' => app::get('syscase')->_('修改时间'),
        ),

       
  ),
    'primary' => 'essaycat_id',
    'comment' => app::get('syscase')->_('解决方案文章类型表'),
);
