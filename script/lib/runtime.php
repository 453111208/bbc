<?php

ob_implicit_flush(1);

require __DIR__.'/../../bootstrap/start.php';

kernel::$console_output = true;

cacheobject::init(false);

// 时区设置
//$timezone = config::get('app.timezone', 8);
//date_default_timezone_set('Etc/GMT'.($timezone>=0?($timezone*-1):'+'.($timezone*-1)));

if (!defined('BASE_URL')) {
    if ($shell_base_url = app::get('base')->getConf('shell_base_url')) {
        define('BASE_URL', $shell_base_url);
    }else{
        echo 'Please install ecstore first, and login to the backend ';
    }
 }


