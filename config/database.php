<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array(

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */


    'default' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    | 对应原系统: DB_*
    |
    */

    'connections' => array(
        'default' => array(
            'driver'    => 'mysqli',
            'host'      => '%HOST%',
            'dbname'  => '%DATABASE%',
            'user'  => '%USERNAME%',
            'password'  => '%PASSWORD%',
            'charset'   => 'utf8',
            //'collation' => 'utf8_general_ci',
        ),
        /*
        'app|systrade' => array(
            'master' => array('user' => '', 'password' => '', 'host' => '', 'dbname' => '','charset'   => 'utf8'),
            'slaves' => array(
                array('user' => 'slave2', 'password'=>'', 'host' => '', 'dbname' => '','charset'   => 'utf8'),
                array('user' => 'slave2', 'password'=>'', 'host' => '', 'dbname' => '','charset'   => 'utf8'),
             ),
            'driver'    => 'mysqli',
        ),
        */

    ),
);

