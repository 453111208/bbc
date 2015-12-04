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
        'node_id' =>array (
            'type' => 'number',
            'required' => true,
            'comment'=> app::get('syscontent')->_('节点id'),
            //'pkey' => true,
            'autoincrement' => true,
            'width' => 10,
            'editable' => false,
            'in_list' => true,
        ),
        'parent_id' =>array (
            'type' => 'number',
            'required' => true,
            'default' => 0,
            'comment'=> app::get('syscontent')->_('父节点'),
            'width' => 10,
            'editable' => true,
            'in_list' => true,
        ),
        'node_depth' => array(
            'type' => 'smallint',
            'required' => true,
            'default' => 0,
            'comment' => app::get('syscontent')->_('节点深度'),
            'editable' => false,
        ),
        'node_name' =>array (
            'type' => 'string',
            'required' => true,
            'default'=>'',
            'comment'=> app::get('syscontent')->_('节点名称'),
            'is_title' => true,
            'editable' => true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'node_path'=>array (
            'type' => 'string',
            'comment'=> app::get('syscontent')->_('节点路径'),
            'editable' => false,
            'in_list' => false,
        ),
        'has_children' => array(
            'type' => 'bool',
            'default' => 0,
            'required' => true,
            'comment' => app::get('syscontent')->_('是否存在子节点'),
            'editable' => false,
            'in_list' => false,
        ),
        'ifpub'=>array (
            'type' => 'bool',
            'default' => 0,
            'required' => true,
            'comment' => app::get('syscontent')->_('发布'),
            'editable' => true,
            'in_list' => true,
        ),
        'order_sort'=> array (
            'type' => 'number',
            'required' => true,
            'default' => 0,
            'editable' => true,
            'comment' => app::get('syscontent')->_('排序'),
        ),
        'modified'=> array (
            'type' => 'time',
            'editable' => true,
            'comment' => app::get('syscontent')->_('修改时间'),
        ),

    ),
    'primary' => 'node_id',
    'comment' => app::get('syscontent')->_('文章节点表'),
);
