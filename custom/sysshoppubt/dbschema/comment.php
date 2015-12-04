<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return  array(
    'columns' => array(
        'comment_id' => array(
            'type' => 'bigint',
            'unsigned' => true,
            'required' => true,
            'autoincrement' => true,
            'comment' => app::get('sysshoppubt')->_('评价ID'),
        ),
        'user_id' =>
        array(
            'type' => 'table:account@sysuser',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysshoppubt')->_('会员'),
        ),
        'user_name' =>
        array(
            'type' => 'string',
            'label' => app::get('sysshoppubt')->_('会员名称'),
        ),
        'shop_id' =>
        array(
            'type' => 'table:shop@sysshop',
            'label' => app::get('sysshoppubt')->_('所属企业'),
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysshoppubt')->_('企业ID'),
        ),
        
        'type' => array(
            'type' => array(
                '0' => app::get('sysshoppubt')->_('标准'),
                '1' => app::get('sysshoppubt')->_('竞价'),
                '2' => app::get('sysshoppubt')->_('招标'),
            ),
            'default' => '0',
            'comment' => app::get('sysshoppubt')->_('类型'),
            'label' => app::get('sysshoppubt')->_('类型'),
            'width' => 110,
            'in_list'=>true,
            'default_in_list'=>true,
            'is_title' => true,
            'order' => 2,
        ),
        'item_id' => array(
            'type' => 'number',
            'required' => true,
            'comment' => app::get('sysshoppubt')->_('评论的交易ID'),
        ),
        'item_title' => array(
            'type' => 'string',
            'length' => 60,
            'required' => true,
            'default' => '',
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysshoppubt')->_('交易标题'),
        ),
        'content' => array(
            'type' => 'text',
            'default' => '',
            'label' => app::get('sysshoppubt')->_('评价内容'),
            'in_list' => true,
            'default_in_list' => false,
        ),
        'is_lock' => array(//1为屏蔽 0为不屏蔽
            'type' => 'bool',
            'default' => '0',
            'comment' => app::get('sysshoppubt')->_('该评价是否被屏蔽'),
        ),
        'created_time' =>
        array(
            'type' => 'time',
            'label' => app::get('sysshoppubt')->_('创建时间'),
            'width' => '100',
            'in_list' => true,
            'default_in_list' => true,
        ),
    ),
    'primary' => 'comment_id',
    'comment' => app::get('sysshoppubt')->_('交易评论列'),
);

