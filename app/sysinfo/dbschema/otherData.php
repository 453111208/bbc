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
        'data_id' =>array (
            'type' => 'number',
            'required' => true,
            'comment'=> app::get('sysinfo')->_('数据ID'),
            'autoincrement' => true,
            'width' => 50,
            'order'=>1,
        ),
        'sort' =>array (
            'type' => 'string',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysinfo')->_('类别'),
            'order'=>2,
        ),
        'market' =>array (
            'type' => 'string',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysinfo')->_('现货金属行情'),
            'order'=>3,
        ),
        'max_price' =>array (
            'type' => 'string',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysinfo')->_('价格区间上限'),
            'order'=>4,
        ),
        'min_price' =>array (
            'type' => 'string',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysinfo')->_('价格区间下限'),
            'order'=>5,
        ),
        'price' =>array (
            'type' => 'string',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysinfo')->_('均价'),
            'order'=>6,
        ),
        'price_run' =>array (
            'type' => 'string',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysinfo')->_('涨跌幅度'),
            'order'=>7,
        ),
        'date' =>array (
            'type' => 'string',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'label' => app::get('sysinfo')->_('上传日期'),
            'editable' => false,
            'width' => 130,
            'in_list' => true,
            'default_in_list' => true,
            'order'=>8,
        ),
        'note' =>array (
            'type' => 'string',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysinfo')->_('备注'),
            'order'=>9,
        ),
        
  ),
    'primary' => 'data_id',
    'comment' => app::get('sysinfo')->_('行情其他数据主表'),
);
