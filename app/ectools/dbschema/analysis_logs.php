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
        'id' => 
        array (
            'type' => 'number',
            'required' => true,
            //'pkey' => true,
            'autoincrement' => true,
            'editable' => false,
            'comment' => app::get('ectools')->_('ectools统计日志ID'),
        ),
        'analysis_id' => 
        array (
            'type' => 'number',
            'required' => true,
            'comment' => app::get('ectools')->_('ectools统计ID'),
        ),
        'type' => 
        array (
            'type' => 'number',
            'required' => true,
            'label' => app::get('ectools')->_('类型'),
            'default' => 0,
        ),
        'target' => 
        array (
            'type' => 'number',
            'required' => true,
            'label' => app::get('ectools')->_('指标'),
            'default' => 0,
        ),
        'flag' => 
        array (
            'type' => 'number',
            'required' => true,
            'label' => app::get('ectools')->_('标识'),
            'default' => 0,
        ),
        'value' => 
        array (
            'type' => 'float',
            'required' => true,
            'label' => app::get('ectools')->_('数据'),
            'default' => 0,
        ),
        'time' => 
        array (
            'type' => 'time',
            'required' => true,
            'label' => app::get('ectools')->_('时间'),
        ),
    ),
    
    'primary' => 'id',
    'index' => array(
        'ind_analysis_id' => ['columns' => ['analysis_id']],
        'ind_type' => ['columns' => ['type']],
        'ind_target' => ['columns' => ['target']],
        'ind_time' => ['columns' => ['time']],
    ),
    'comment' => app::get('ectools')->_('ectools统计日志'),
);

