<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
return  array(
    'columns'=>
    array(
        'guimo_id'=>
        array(
            'type'=>'number',
            //'pkey'=>true,
            'autoincrement' => true,
            'comment' => app::get('sysshop')->_('企业规模ID'),
        ),

      'guimo' =>array(
            'type' => 'string',
            'length' => 100,
            'required' => true,
            'label' => app::get('sysshop')->_('企业规模'),
            'width' => 310,
            'in_list' => true,
            'is_title' => true,
            'default_in_list' => true,
        ),
    ),
    
    'primary' => 'guimo_id',
    'comment' => app::get('sysshop')->_('企业规模表'),
);
