<?php
return  array(
    'columns'=> array(
        'shop_id' => array(
            'type' => 'string',
            'required' => true,
            'label' => app::get('sysopen')->_('对应的商户id'),
            'comment' => app::get('sysopen')->_('对应的商户id'),
        ),
        'develop_mode' => array(
            'type' => array(
                'DEVELOP' => '开发者模式',
                'PRODUCT' => '运营商模式',
            ),
            'required' => true,
            'label' => app::get('sysopen')->_('开发者模式配置'),
            'comment' => app::get('sysopen')->_('开发者模式配置'),
        ),
    ),
    'primary' => 'shop_id',
    'comment' => app::get('sysopen')->_('店铺请求开放api的key和secret'),
);

