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
        'offer_id' =>array (
            'type' => 'number',
            'required' => true,
            'comment'=> app::get('sysinfo')->_('报价数据ID'),
            'autoincrement' => true,
            'width' => 50,
            'order'=>1,
        ),
        'title' =>array (
            'type' => 'string',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysinfo')->_('产品名称'),
            'order'=>2,
        ),
        'price' =>array (
            'type' => 'string',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysinfo')->_('价格'),
            'order'=>3,
        ),
        'date' =>array (
            'type' => 'string',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'label' => app::get('sysinfo')->_('时间'),
            'editable' => false,
            'width' => 130,
            'in_list' => true,
            'default_in_list' => true,
            'order'=>4,
        ),
        'area' =>array (
            'type' => 'string',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysinfo')->_('所在地'),
            'order'=>5,
        ),
        'company' =>array (
            'type' => 'string',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysinfo')->_('公司'),
            'order'=>6,
        ),
  ),
    'primary' => 'offer_id',
    'comment' => app::get('sysinfo')->_('废电子电器商家报价数据主表'),
);
