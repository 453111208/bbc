<?php
/**
 * ShopEx licence
 * ajx
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array (
    'columns' =>
    array (
        'tmpl_name' => array (
            //'type' => 'varchar(100)',
            'type' => 'string',
            'length' => 100,
            //'pkey' => true,
            'required' => true,
            'comment' => app::get('system')->_('模版名称'),
        ),
        'content' => array(
            'type'=>'text',
            'label' =>app::get('system')->_('内容'),
            'default' => 0,
            'comment' => app::get('system')->_('模板内容'),
        ),
        'modified_time' =>
        array (
            'type' => 'last_modify',
            'label' => app::get('system')->_('更新时间'),
            'width' => 110,
            'order' => 50,
            'editable' => false,
            'orderby' => true,
            'in_list' => true,
            'default_in_list' => false,
        ),        
        'active' => array(
            'type' => 'bool',
            //'type'=>"enum('true', 'false')",
            'default' => 1,
            'comment' => app::get('system')->_('是否激活'),
        ),

    ),
    
    'primary' => 'tmpl_name',
    'comment' => app::get('system')->_('邮件短信模板'),
);
