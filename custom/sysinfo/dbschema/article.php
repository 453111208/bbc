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
        'article_id' =>array (
            'type' => 'number',
            'required' => true,
            'comment'=> app::get('sysinfo')->_('资讯ID'),
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
            'label' => app::get('sysinfo')->_('资讯标题'),
            'order'=>2,
        ),
        'subhead' =>array (
            'type' => 'string',
            'label' => app::get('sysinfo')->_('文章摘要'),
        ),
        'article_logo' => array(
            'type' => 'string',
            'label' => app::get('sysinfo')->_('资讯logo'),
            'order'=>5,
            'comment' => app::get('sysinfo')->_('资讯默认logo'),
        ),
        'platform' => array(
            'type' => array(
                'pc' => app::get('sysinfo')->_('电脑端'),
                'wap' => app::get('sysinfo')->_('移动端'),
            ),
            'required' => true,
             'default' => 'pc',

            'label' => app::get('sysinfo')->_('发布终端'),
            'order'=>3,
        ),
        'node_id' =>array (
            'type' => 'number',
            'required' => true,
           /* 'in_list' => true,
            'default_in_list' => true,*/
            'label' => app::get('sysinfo')->_('节点id'),
            'order'=>4,
        ),
        'pubtime' => array(
            'type' => 'time',
            'comment' => app::get('sysinfo')->_('发布时间（无需精确到秒）'),
            'label' => app::get('sysinfo')->_('发布时间'),
            'editable' => true,
            'width' => 130,
            'order'=>6,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'modified' =>array (
            'type' => 'time',
            'comment' => app::get('sysinfo')->_('更新时间（精确到秒）'),
            'label' => app::get('sysinfo')->_('发布时间'),
            'editable' => false,
            'width' => 130,

            'order'=>7,
        ),
        'ifpub' => array(
            'type' => 'bool',
            'default' => 0,
            'comment' => app::get('sysinfo')->_('发布'),
            'editable' => true,
            'width' => 40,
            'order'=>8,
        ),
        'status' =>array(
            'type' => array(
                '0' => app::get('sysinfo')->_('未审核'),
                '1' => app::get('sysinfo')->_('已审核'),
            ),
            'default' => '0',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysinfo')->_('发布状态'),
            'label' => app::get('sysinfo')->_('发布状态'),
            'width' => 40,
            'order'=>9,
        ),
        'source' =>array (
             'type' => 'string',
             'searchtype' => 'has',
             'filtertype' => 'normal',
             'filterdefault' => 'true',
             'required' => true,
             'in_list' => true,
             'default_in_list' => true,
             'label' => app::get('sysinfo')->_('资讯来源'),
             'order'=>10,
         ),
        'towhere' =>array(
            'type' => array(
                '0' => app::get('sysinfo')->_('否'),
                '1' => app::get('sysinfo')->_('是'),
            ),
            'default' => '0',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysinfo')->_('是否发布到资讯中心'),
            'label' => app::get('sysinfo')->_('是否发布到资讯中心'),
            'width' => 40,
        ),
        'author'=>array (
            'type' => 'string',
            'comment'=> app::get('sysinfo')->_('作者'),
            'in_list' => true,
             'searchtype' => 'has',
             'filtertype' => 'normal',
             'filterdefault' => 'true',
            'default_in_list' => true,
            'order'=>11,
        ),
        'content' =>array (
            'type' => 'text',
            'comment'=> app::get('sysinfo')->_('资讯内容'),
            'editable' => true,
        ),
        'user_id' =>array (
            'type' => 'number',
            'comment'=> app::get('sysinfo')->_('会员ID'),
            'width' => 100,
        ),
        'click_rate' => array(
            'type' => 'number', 
            'default' => 0,
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysinfo')->_('点击量'),
            'label' => app::get('sysinfo')->_('点击量'),
        ),
        'essaycat_id' =>array (
            'type' => 'number',
            'comment'=> app::get('syscase')->_('解决方案文章类型ID'),
            'width' => 50,
            'order'=>1,
        ),
        'istop' =>array(
            'type' => array(
                '0' => app::get('sysexpert')->_('否'),
                '1' => app::get('sysexpert')->_('是'),
            ),
            'default' => '0',
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysexpert')->_('是否置顶'),
            'label' => app::get('sysexpert')->_('是否置顶'),
            'width' => 40,
            'order'=>11,
        ),
        'ishot'=>array(
            'type' => array(
                '0' => app::get('sysexpert')->_('否'),
                '1' => app::get('sysexpert')->_('是'),
            ),
            'default' => '0',
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysexpert')->_('是否热门'),
            'label' => app::get('sysexpert')->_('是否热门'),
            'width' => 40,
            'order'=>11,
        ),

  ),
    'primary' => 'article_id',
    'comment' => app::get('sysinfo')->_('资讯主表'),
);
