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
    | 默认缓存处理
    |--------------------------------------------------------------------------
    |
    | 默认缓存处理类
    | 目前支持 base_kvstore_mongodb | base_kvstore_mysql | base_kvstore_filesystem | base_kvstore_memached
    | 对应原系统:  KVSTORE_STORAGE
    |
    */
    'default' => 'base_kvstore_filesystem',

    /*
    |--------------------------------------------------------------------------
    | 是否持久化处理
    |--------------------------------------------------------------------------
    |
    | 是否持久化处理
    |
    */
    'presistent' => true,
    /*
    |--------------------------------------------------------------------------
    | kv存储key前缀
    |--------------------------------------------------------------------------
    |
    | kv存储key前缀
    |
    */
    'prefix' => 'default',

    /*
    |--------------------------------------------------------------------------
    | mongodb配置
    |--------------------------------------------------------------------------
    |
    | hosts 支持多实例. 目前支持"mongodb://${username}:${password}@localhost" ,
    | "mongodb:///tmp/mongo-27017.sock" 两种风格
    | options MongoClient::__construct 第二个参数 An array of options for the
    | connection
    */
    'base_kvstore_mongodb' => array(
        'hosts' => array(
           // 'mongodb:///tmp/mongo-27017.sock',
            'mongodb://localhost:27017'
        ),

        'options' => array(
            'connect' => true,
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | mongodb配置
    |--------------------------------------------------------------------------
    |
    | hosts 支持多实例. 目前支持"mongodb://${username}:${password}@localhost" ,
    | "mongodb:///tmp/mongo-27017.sock" 两种风格
    | options MongoClient::__construct 第二个参数 An array of options for the
    | connection
    */

    'base_kvstore_memcached' => array(
        'hosts' => array(
            'unix:///tmp/memcached.sock',
            '127.0.0.1:11211'
        ),
    ),

    /*
    |--------------------------------------------------------------------------
    | redis配置
    |--------------------------------------------------------------------------
    |
    */
    'base_kvstore_redis' => '127.0.0.1:6379',
);
