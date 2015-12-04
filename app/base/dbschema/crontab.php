<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author afei, braynt
 */
return array (
    'columns' =>
    array (
        'id' => array(
            //'type'=>'varchar(100)',
            'type' => 'string',
            'length' => 100,
            //'pkey'=> true,
            'required'=> true,
            'label' => app::get('base')->_('定时任务ID'),
            'editable'=> false,
            'is_title'=> true,
            'in_list'=> true,
            'default_in_list'=> false,
            'width' => 70,            
            'order' => 10,
        ),


        'description' => array(
            'type' => 'string',
            
            'required'=>true,
            'label' => app::get('base')->_('描述'),
            'in_list' => true,
            'default_in_list' => true,
            'order' => 15,
        ),
        
        'enabled' => array(
            'type'=>'bool',
            'default'=>1,
            'label' => app::get('base')->_('开启'),
            'required'=>true,
            'in_list' => true,
            'default_in_list' => true,
            'order' => 20,            
        ),
        
        'schedule' => array(
            'type'=>'string',
            
            'label' => app::get('base')->_('规则'),
            'required'=>true,
            'in_list' => true,
            'default_in_list' => true,
            'order' =>30,
        ),
        'last' => array(
            'type'=>'time',
            'label' => app::get('base')->_('最后执行时间'),
            'required'=>true,
            'in_list' => true,
            'default_in_list' => true,
        ),
        'app_id' => array (
            //'type' => 'varchar(32)',
            'type' => 'string',
            'length' => '32',
            
            'required' => true,
            'width' => 50,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('base')->_('app应用'),        
        ),
        'class' => array(
            //'type'=>'varchar(100)',
            'type' => 'string',
            'length' => '100',
            
            'required'=>true,
            'label' => app::get('base')->_('定时任务类名'),
            'editable' => false,
            'in_list'=>true,
            'default_in_list'=>false,
            'order' => 100,
        ),
        'type' => array(
            'type'=> array(
                'custom' => '客户自定义',
                'system' => '系统内置'),
            'label' => app::get('base')->_('定时器类型'),
            'in_list' => true,
            'default_in_list' => false,
        ),
    ),
    'primary' => 'id',
    'comment' => app::get('base')->_('定时任务表'),
);
