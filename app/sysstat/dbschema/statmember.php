<?php
return  array(
    'columns'=>array(
        'statm_id'=>array(
            //'type'=>'bigint unsigned',
            'type' => 'bigint',
            'unsigned' => true,
            //'pkey'=>true,
            'autoincrement' => true,
            'required' => true,
            'label' => 'id',
            'comment' => app::get('sysstat')->_('会员数据统计id 自赠'),
            'order' => 1,
        ),

        'newuser'=>array(
            //'type'=>'varchar(50)',
            'type' => 'number',
            'default' => 0,
            'label' => app::get('sysstat')->_('新增会员数'),
            'comment' => app::get('sysstat')->_('新增会员数'),
            'order' => 2,
        ),
        'accountuser'=>array(
            //'type'=>'varchar(50)',
            'type' => 'number',
            'default' => 0,
            'label' => app::get('sysstat')->_('会员数总数'),
            'comment' => app::get('sysstat')->_('会员数总数'),
            'order' => 5,
        ),
       
        'shopnum'=>array(
            //'type'=>'varchar(50)',
            'type' => 'number',
            'default' => 0,
            'label' => app::get('sysstat')->_('新增店铺数'),
            'comment' => app::get('sysstat')->_('新增店铺数'),
            'order' => 3,
        ),
        'shopaccount'=>array(
            //'type'=>'varchar(50)',
            'type' => 'number',
            'default' => 0,
            'label' => app::get('sysstat')->_('店铺数'),
            'comment' => app::get('sysstat')->_('店铺数'),
            'order' => 4,
        ),
        
        'sellernum'=>array(
            //'type'=>'varchar(50)',
            'type' => 'number',
            'default' => 0,
            'label' => app::get('sysstat')->_('新增商家数'),
            'comment' => app::get('sysstat')->_('新增商家数'),
            'order' => 5,
        ),
        'selleraccount'=>array(
            //'type'=>'varchar(50)',
            'type' => 'number',
            'default' => 0,
            'label' => app::get('sysstat')->_('商家数'),
            'comment' => app::get('sysstat')->_('商家数'),
            'order' => 6,
        ),
        'createtime'=>array(
            'type'=>'time',
            'comment' => app::get('sysstat')->_('创建时间'),
        ),
    ),
    
    'primary' => 'statm_id',
);
