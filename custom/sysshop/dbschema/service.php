<?php
/**
 * ShopEx licence 第三方文章列表
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
            'comment'=> app::get('sysshop')->_('文章ID'), 
            'autoincrement' => true, 
            'width' => 50, 
        ),
        'title' =>array ( 
            'type' => 'string', 
            'searchtype' => 'has',
            'filtertype' => 'normal', 
            'filterdefault' => 'true',
            'required' => true, 
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysshop')->_('文章标题'), 
            'order'=>2,
        ),
        'article_logo' => array( 
            'type' => 'string', 
            'label' => app::get('sysshop')->_('文章logo'), 
            'comment' => app::get('sysshop')->_('文章默认logo'),  
            'order'=>3, 
        ),
        'pubtime' => array(  
            'type' => 'time',
            'in_list' => true, 
            'default_in_list' => true, 
            'comment' => app::get('sysshop')->_('发布时间'), 
            'label' => app::get('sysshop')->_('发布时间'),  
            'editable' => true,
            'width' => 130,
            'order'=>6, 
        ),
        'modified' =>array (  
            'type' => 'time',
            'comment' => app::get('sysshop')->_('更新时间'), 
            'label' => app::get('sysshop')->_('更新时间'),  
            'editable' => false,
            'width' => 130,
            'in_list' => true,
            'default_in_list' => true,
            'order'=>7, 
        ),
        'ifpub' => array( 
            'type' => 'bool', 
            'default' => 0, 
            'comment' => app::get('sysshop')->_('发布'),
            'label' => app::get('sysshop')->_('是否发布'),
            'editable' => true,
            'width' => 40,
            'in_list' => true,
        ),
        'abstract' =>array (
            'type' => 'text',
            'comment'=> app::get('sysshop')->_('简介'), 
            'editable' => true, 
        ),
        'content' =>array (  
            'type' => 'text',
            'comment'=> app::get('sysshop')->_('文章内容'), 
            'editable' => true, 
        ),
        'count' =>array (  
            'type' => 'number',
            'in_list' => true,
            'default' => 0, 
            'default_in_list' => true,
            'comment'=> app::get('sysshop')->_('点击量'), 
            'label'=> app::get('sysshop')->_('点击量'), 
            'editable' => true, 
            'order'=>8, 
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
    'comment' => app::get('sysshop')->_('第三方服务列表'), 
);
