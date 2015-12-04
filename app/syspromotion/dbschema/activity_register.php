<?php
return array(
    'columns' => array(
        'id' => array(
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'label' => app::get('syspromotion')->_('id'),
            'comment' => app::get('syspromotion')->_('id'),
        ),
        'shop_id' => array(
            'type' => 'number',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('syspromotion')->_('店铺'),
            'comment' => app::get('syspromotion')->_('店铺id'),
        ),
        'activity_id' => array(
            'type' => 'number',
            'required' => true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('syspromotion')->_('活动'),
            'comment' => app::get('syspromotion')->_('活动id'),
        ),
        'verify_status' => array(
            'type' => array(
                'pending' => '待审核',
                'refuse' => '审核被拒绝',
                'agree' => '审核通过',
                // 'again' => '再次申请',
            ),
            'default' => 'pending',
            'required'=> true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('syspromotion')->_('审核状态'),
            'comment' => app::get('syspromotion')->_('审核状态'),
        ),
        'valid_status' => array(
            'type' => 'bool',
            'default' => '1', //0失效，1有效
            'required'=> true,
            'in_list' => true,
            'default_in_list' => true,
            'label' => app::get('syspromotion')->_('有效状态'),
            'comment' => app::get('syspromotion')->_('有效状态'),
        ),
        'refuse_reason' => array(
            'type' => 'string',
            'default' => '',
            'in_list' => true,
            'label' => app::get('syspromotion')->_('拒绝原因'),
            'comment' => app::get('syspromotion')->_('拒绝原因'),
        ),
        'modified_time' => array(
            'type' => 'time',
            'in_list' => true,
            'default_in_list' => true,
            'width' => '100',
            'label' => app::get('syspromotion')->_('报名更新时间'),
            'comment' => app::get('syspromotion')->_('报名更新时间'),
        ),
    ),
    'primary' => ['id'],
    'index' => array(
        'ind_activitywithshop' => [
            'columns' => ['activity_id', 'shop_id'],
            'prefix' => 'unique',
        ],
    ),
    'comment' => app::get('syspromotion')->_('活动报名表'),
);

