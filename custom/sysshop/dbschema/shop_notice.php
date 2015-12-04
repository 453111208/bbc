<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
return  array(
    'columns'=>
    array(
        'notice_id'=>
        array(
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'width' => 50,
            'order'=>1,
            'comment' => app::get('sysshop')->_('企业通知ID'),
            'label' => app::get('sysshop')->_('企业通知ID'),
        ),
        'notice_title'=>
        array(
            'type' => 'string',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'order'=>2,
            'comment' => app::get('sysshop')->_('企业通知标题'),
            'label' => app::get('sysshop')->_('企业通知标题'),
        ),
        'notice_content'=>
        array(
            'type' => 'text',
            'required' => true,
            'comment' => app::get('sysshop')->_('企业通知内容'),
            'order'=>3,
        ),
        'notice_type'=>
        array(
            'type' => 'string',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'order'=>4,
            'comment' => app::get('sysshop')->_('企业通知类型'),
            'label' => app::get('sysshop')->_('企业通知类型'),
        ),
        'shop_id'=>
        array(
            'type' => 'number',
            'required' => true,
            'label' => app::get('sysshop')->_('企业id'),
            'order'=>5,
        ),
        'admin_id'=>
        array(
            'type' => 'number',
            'required' => false,
            'label' => app::get('sysshop')->_('操作者的id'),
            'order'=>6,
        ),
        'createtime'=>
        array(
            'type' => 'time',
            'comment' => app::get('sysshop')->_('企业通知创建时间'),
            'label' => app::get('sysshop')->_('企业通知创建时间'),
            'editable' => false,
            'width' => 130,
            'in_list' => true,
            'default_in_list' => true,
            'order'=>7,
        ),
        'modified_time' =>
        array (
            'type' => 'time',
            'comment' => app::get('sysshop')->_('企业通知最后修改时间'),
            'label' => app::get('sysshop')->_('企业通知最后修改时间'),
            'editable' => false,
            'width' => 130,
            'in_list' => true,
            'default_in_list' => true,
            'order'=>8,
        ),
         'is_read' => array(
            'type' => 'bool',
            'required' => true,
            'default' => 0,
            'label' => app::get('sysshop')->_('是否已读'),
            'width' => 110,
            'in_list' => true,
        ),

    
    ),
    
    'primary' => 'notice_id',
    'comment' => app::get('sysshop')->_('企业通知表'),
);
