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
            //'type' => 'bigint unsigned',
            'type' => 'number',
            //'pkey' => true,
            'label' => 'ID',
            'required' => true,
        ),
        'index_name' =>
        array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            //'pkey' => true,
            'is_title'=>true,
            'label'=>app::get('search')->_('索引名称'),
            'width'=>'200',
            'required' => true,
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'last_modify' =>
        array (
            'type' => 'last_modify',
            'label' => app::get('search')->_('更新时间'),
            'width' => 110,
            'in_list' => true,
            'orderby' => true,
        ),
    ),
    'primary' => ['id', 'index_name'],
    'index' => array(
        'ind_last_modify' => ['columns' => ['last_modify']],
    ),
);
