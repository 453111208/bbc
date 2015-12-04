<?php
/**
 * ShopEx licence
 * ajx
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 *
 * 系统配件（邮件短信等配置）
 */

return array(

    'messenger' =>array(
        /*
        |--------------------------------------------------------------------------
        | 电子邮件配置
        |--------------------------------------------------------------------------
         */
        'email' => array(
            'label' => '电子邮件',
            'display' => true,
            'version' => '$ver$',
            'isHtml' => true,
            'hasTitle' => true,
            'allowMultiTarget' => false,
            'targetSplit' => ',',
            'dataname' => 'email',
            'debug' => false,
            'class' => 'system_messenger_email',
        ),
        
        /*
        |--------------------------------------------------------------------------
        | 手机短信配置
        |--------------------------------------------------------------------------
         */
        'sms' => array(
            'label' => '手机短信',
            'display' => true,
            'version' => '$ver$',
            'isHtml' => false,
            'hasTitle' => false,
            'allowMultiTarget' => false,
            'withoutQueue' => false,
            'dataname' => 'mobile',
            'sms_service_ip' => '124.74.193.222',
            'sms_service' => 'http://idx.sms.shopex.cn/service.php',
            'class' => 'system_messenger_sms',
        ),
    ),
    'actions' => array(

        /*
        |--------------------------------------------------------------------------
        | 身份验证
        |--------------------------------------------------------------------------
         */
        'account-member' => array(
            'label' => '身份验证',
            'email' => 'true',
            'sms' => 'true',
            'sendType' => 'notice',
            'use_reply'=>'false',
            'varmap' => '验证码<{$vcode}>',
        ),

        /*
        |--------------------------------------------------------------------------
        | 手机注册短信验证
        |--------------------------------------------------------------------------
         */
        'account-signup' => array(
            'label' => '手机注册短信验证',
            'email' => 'false',
            'sms' => 'true',
            'sendType' => 'notice',
            'use_reply'=>'false',
            'varmap' => '验证码<{$vcode}>',
        ),

        /*
        |--------------------------------------------------------------------------
        | 手机注册短信找回密码验证
        |--------------------------------------------------------------------------
         */
        'account-lostPw' => array(
            'label' => '找回密码',
            'email' => 'true',
            'sms' => 'true',
            'sendType' => 'notice',
            'use_reply'=>'false',
            'varmap' => '验证码<{$vcode}>',
        ),

        /*
        |--------------------------------------------------------------------------
        | 解绑
        |--------------------------------------------------------------------------
         */
        'account-unmember' => array(
            'label' => '解绑手机邮箱',
            'email' => 'true',
            'sms' => 'true',
            'sendType' => 'notice',
            'use_reply'=>'false',
            'varmap' => '验证码<{$vcode}>',
        ),

        /*
        |--------------------------------------------------------------------------
        | 邮件通知
        |--------------------------------------------------------------------------
         */
        'user-item' => array(
            'label' => '到货通知邮箱',
            'email' => 'true',
            'sms' => 'false',
            'sendType' => 'notice',
            'use_reply'=>'false',
            'varmap' => '邮件<{$vcode}>',
        ),


    ),
);

