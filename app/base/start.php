<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

error_reporting(E_ERROR | E_USER_ERROR | E_PARSE | E_COMPILE_ERROR);


if ( ! extension_loaded('mcrypt'))
{
	echo 'Mcrypt PHP extension required.'.PHP_EOL;

	exit(1);
}

kernel::startExceptionHandling();

//todo:
//if ($env != 'testing') ini_set('display_errors', 'Off');

/*
|--------------------------------------------------------------------------
| Set The Default Timezone
|--------------------------------------------------------------------------
|
| Here we will set the default timezone for PHP. PHP is notoriously mean
| if the timezone is not explicitly set. This will be used by each of
| the PHP date and date-time functions throughout the application.
|*/
$config = config::get('app');
$timezone = $config['timezone']?:8;
date_default_timezone_set('Etc/GMT'.($timezone>=0?($timezone*-1):'+'.($timezone*-1)));

//$routes = BOOT_DIR.'/routes.php';
//if (file_exists($routes)) require $routes;
