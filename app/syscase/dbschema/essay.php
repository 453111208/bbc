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
        'essay_id' =>array (
            'type' => 'number',
            'required' => true,
            'comment'=> app::get('syscase')->_('解决方案文章ID'),
            'autoincrement' => true,   //序号自增
            'width' => 50,
            'order'=>1,
            ),
        'title' =>array (
            'type' => 'string',     //字段类型
            'searchtype' => 'has',    //搜索的类型
            'filtertype' => 'normal', //高级筛选的过滤类型，normal按type来生成过滤
            'filterdefault' => 'true',//默认在高级筛选中显示
            'required' => true,   //必填项，不能为空，默认为falsse
            'in_list' => true,  //显示在列表项中
            'default_in_list' => true,  //默认显示在列表项中
            'label' => app::get('syscase')->_('文章名称'),  //显示的名称
            'order'=>2,   //列表中的权重，越小越靠前！
            ),
        'essaycat_id' => array(
            'type' => 'table:essaycat@syscase',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,  //显示在列表项中
            'default_in_list' => true,  //默认显示在列表项中
            'label' => app::get('syscase')->_('类型'),
            'comment' => app::get('syscase')->_('类型'),
            'order'=>5,
            ),
        'pubtime' => array(
            'type' => 'time',
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('syscase')->_('发布时间（无需精确到秒）'),
            'label' => app::get('syscase')->_('发布时间'),
            'editable' => true,
            'width' => 130,
            'order'=>6,
            ),
        'context' => array(
            'type' => 'text',
            'required' => true,
            'in_list' => true,
            'label' => app::get('syscase')->_('文章内容'),
            'comment' => app::get('syscase')->_('文章内容'),
            'order'=>5,
        ),
       

  ),
    'primary' => 'essay_id',
    'comment' => app::get('syscase')->_('解决方案文章表'),
);
