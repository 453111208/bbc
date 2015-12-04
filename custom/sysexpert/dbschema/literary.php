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
        'literary_id' =>array (
            'type' => 'number',
            'required' => true,
            'comment'=> app::get('sysexpert')->_('名人专家文章ID'),
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
            'label' => app::get('sysexpert')->_('文章名称'),  //显示的名称
            'order'=>2,   //列表中的权重，越小越靠前！
        ),
        'expert_id' =>array (
            'type' => 'table:expert@sysexpert',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,  //显示在列表项中
            'default_in_list' => true,  //默认显示在列表项中
            'label' => app::get('sysexpert')->_('名人专家名称'),
            'comment' => app::get('sysexpert')->_('名人专家名称ID'),
            'order'=>2,
        ),
         'literarycat_id' => array(
            'type' => 'table:literarycat@sysexpert',
            'required' => true,
            'in_list' => true,  //显示在列表项中
            'default_in_list' => true,  //默认显示在列表项中
            'label' => app::get('sysexpert')->_('文章类型'),
            'comment' => app::get('sysexpert')->_('文章类型ID'),
            'order'=>5,
        ),
         'literary_logo' => array(
            'type' => 'string',
            'in_list' => true,
            'label' => app::get('sysexpert')->_('文章列表图片'),
            'order'=>9,
            'comment' => app::get('sysexpert')->_('文章列表图片'),
        ),
       'pubtime' => array(
            'type' => 'time',
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysexpert')->_('发布时间（无需精确到秒）'),
            'label' => app::get('sysexpert')->_('发布时间'),
            'editable' => true,
            'width' => 130,
            'order'=>6,
        ),
        'abstract' => array(
            'type' => 'text',
            'required' => true,
            'in_list' => true,
            'label' => app::get('sysexpert')->_('文章简介'),
            'comment' => app::get('sysexpert')->_('文章简介'),
            'order'=>7,
        ),
        'context' => array(
            'type' => 'text',
            'required' => true,
            'in_list' => true,
            'label' => app::get('sysexpert')->_('文章内容'),
            'comment' => app::get('sysexpert')->_('文章内容'),
            'order'=>5,
        ),
        'modified'=> array (
            'type' => 'time',
            'editable' => true,
            'in_list' => true,
            'label' => app::get('sysexpert')->_('修改时间'),
            'comment' => app::get('sysexpert')->_('修改时间'),
        ),
         'click_count' =>array (
            'type' => 'number',
            'in_list' => true,
            'default' => 0,
            'default_in_list' => true,
            'label' => app::get('sysexpert')->_('点击量'),
            'comment'=> app::get('sysexpert')->_('点击量'),
            'order'=>10,
        ),
         
        'towhere' =>array(
            'type' => array(
                '0' => app::get('sysexpert')->_('否'),
                '1' => app::get('sysexpert')->_('是'),
            ),
            'default' => '0',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysexpert')->_('是否发布到名人专家'),
            'label' => app::get('sysexpert')->_('是否发布到名人专家'),
            'width' => 40,
            'order'=>11,
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
    'primary' => 'literary_id',
    'comment' => app::get('sysexpert')->_('专家文章表'),
);
