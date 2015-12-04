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
        'expert_id' =>array (
            'type' => 'number',
            'required' => true,
            'comment'=> app::get('sysexpert')->_('专家ID'),
            'autoincrement' => true,
            'width' => 50,
            'order'=>1,
        ),
        'name' =>array (
            'type' => 'string',
            'searchtype' => 'has',
            'filtertype' => 'normal',
            'filterdefault' => 'true',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('sysexpert')->_('名人专家名称'),
            'order'=>2,
        ),
        'nickname' => array(
            'type' => 'string',
            'label' => app::get('sysexpert')->_('称号'),
            'order'=>3,
            'in_list' => true,
            'default_in_list' => true,
            'comment' => app::get('sysexpert')->_('称号'),
        ),
        'image_logo' => array(
            'type' => 'string',
            'in_list' => true,
            'label' => app::get('sysexpert')->_('头像'),
            'order'=>9,
            'comment' => app::get('sysexpert')->_('头像'),
        ),
         'image_background' => array(
            'type' => 'string',
            'in_list' => true,
            'label' => app::get('sysexpert')->_('背景'),
            'order'=>9,
            'comment' => app::get('sysexpert')->_('背景'),
        ),
        'summary' => array(
            'type' => 'text',
            //'required' => true,
            'in_list' => true,
            'label' => app::get('sysexpert')->_('简介'),
            'comment' => app::get('sysexpert')->_('简介'),
            'order'=>3,
        ),
       'modified'=> array(
            'type' => 'time',
            'editable' => true,
            'in_list' => true,
            'label' => app::get('sysexpert')->_('修改时间'),
            'comment' => app::get('sysexpert')->_('修改时间'),
        ),


  ),
    'primary' => 'expert_id',
    'comment' => app::get('sysexpert')->_('专家表'),
);
