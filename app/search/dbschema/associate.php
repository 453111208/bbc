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
            'autoincrement' => true,
            'label' => 'ID',
            'required' => true,
        ),
        'words' =>
        array (
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'is_title'=>true,
            'label'=>app::get('search')->_('联想词'),
            'width'=>'200',
            'required' => true,
            'in_list'=>true,
            'default_in_list'=>true,
        ),
        'type_id' =>
        array (
            //'type' => 'bigint unsigned',
            'type' => 'number',
            'label' => 'ID',
        ),
        'from_type' =>
        array(
            //'type' => 'varchar(50)',
            'type' => 'string',
            'length' => 50,
            'label' => '来源',
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
    'primary' => 'id',
    'index' => array(
        'ind_last_modify' => ['columns' => ['last_modify']],
    ),
);
