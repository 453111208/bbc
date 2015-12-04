<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2014 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array(
    'columns' => array(
         'enquire_id' => array(
            'type' => 'number',
            'required' => true,
            'autoincrement' => true,
            'comment' => app::get('sysspfb')->_('询价信息ID'),
            'width' => 50,
        ),
        'reqsupp_id' => array(
            'type' => 'number',
            'comment' => app::get('sysspfb')->_('供应或求购ID'),
            'length' => 110,

        ),
         'name' => array(
            'type' => 'string',
            'label' => app::get('sysspfb')->_('联系人'),
            'length' => 110,
            'in_list' => true,
            'default_in_list' => true,
        ),
         'email' => array(
            'type' => 'string',
            'length' => 100,
            'label' => app::get('sysspfb')->_('电子邮件'),
            'in_list' => true,
            'default_in_list' => true,
        ),
         'phone' => array(
            'type' => 'string',
            'length' => 100,
            'label' => app::get('sysspfb')->_('联系方式'),
            'in_list' => true,
            'default_in_list' => true,
        ),
         'workName' => array(
            'type' => 'string',
            'length' => 100,
            'label' => app::get('sysspfb')->_('单位名称'),
            'in_list' => true,
            'default_in_list' => true,
        ),
         'workPhong' => array(
            'type' => 'string',
            'length' => 100,
            'label' => app::get('sysspfb')->_('单位电话'),
            'in_list' => true,
            'default_in_list' => true,
        ),
        'note' => array(
            'type' => 'string',
            'length' => 100,
            'label' => app::get('sysspfb')->_('备注'),
        ),
        'user_id' =>array(
            'type' => 'number',
            'width' => 100,
            'label' => app::get('sysspfb')->_('会员ID'),
        ),
        'ifrequire' => array(
            'type' => array(
                '1' => '供应',
                '2' => '求购',
            ),
            'length' => 100,
            'label' => app::get('sysspfb')->_('询价类型'),
            'in_list' => true,
            'default_in_list' => true,
        ),
    ),
    'primary' => 'enquire_id',
    'comment' => app::get('sysspfb')->_('询价信息发布表'),
); 
