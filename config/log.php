<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
use Monolog\Logger as MonologLogger;

return array(
    /*
    |--------------------------------------------------------------------------
    | 记录等级
    |--------------------------------------------------------------------------
    |
    | 可配置emergency/alert/critical/error/warning/notice/info/debug
    |
    */
    'record_level' => 'debug',
    
    /*
    |--------------------------------------------------------------------------
    | 是否开启mail log
    |--------------------------------------------------------------------------
    |
    | 邮件log bool
    |
    */
    'mail_log' => false,

    /*
    |--------------------------------------------------------------------------
    | 默认驱动
    |
    |--------------------------------------------------------------------------
    |
    | 可配置file/syslog
    |
    */
    'default' => 'file',

    'file' => array(
        'locate' => DATA_DIR.'/logs/site/{date}/{ip}.php'
    ),

	/*
	|--------------------------------------------------------------------------
	| Logging Configuration
	|--------------------------------------------------------------------------
	|
	| Here you may configure the log settings for your application. Out of
	| the box, Laravel uses the Monolog PHP logging library. This gives
	| you a variety of powerful log handlers / formatters to utilize.
	|
	| Available Settings: "single", "daily", "syslog", "errorlog"
	|
	*/
    'log' => 'daily',
);
