<?php

/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return  array(
    'columns'=>array(
        'trust_id'=>array(
            'type'=>'number',
            'required' => true,
            'autoincrement' => true,
            'comment'=>app::get('sysuser')->_('信任id'),
        ),

        'user_id'=>array(
            'type'=>'table:user@sysuser',
            'label'=>app::get('sysuser')->_('会员id'),
            'comment'=>app::get('sysuser')->_('会员id'),
        ),

        'user_flag'=>array(
            'type' => 'string',
            'required' => true,
            'comment'=>app::get('sysuser')->_('对应信任登陆方的唯一标识'),
        ),
    ),

    'primary' => 'trust_id',
    'index' => array(
        'ind_bind_uniq' => ['columns' => ['user_id', 'user_flag'], 'prefix' => 'unique'],
    ),
);
