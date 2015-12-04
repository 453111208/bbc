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
    | 提供搜索引擎插件
    |--------------------------------------------------------------------------
    | 对应原系统的services ID为 server.search_server 配置
    |
    */
    'server' => array(
        'search_policy_sphinx',
        'search_policy_mysql', //开发的时候启用，则不需要配置sphinx
    ),

    /*
    |--------------------------------------------------------------------------
    | 搜索索引对应关系表
    |--------------------------------------------------------------------------
    | 在搜索的时候，指定对应的索引
    */
    'index' => array(
        'item' => array(
            'app' => 'sysitem',
            'model'=>'item',//用于兼容mysql搜索
            'name'=>'sysitem_item',//sphinx中对于商品搜索的索引名称
            'extends'=>'sysitem_search_item'//如果是mysql搜索，则直接进入该类进行扩展，另外一个作用为sphinx搜索返回的值进行扩展
        ),//商品搜索
        'brand' => array(
            'app' => 'syscategory',
            'model'=>'brand',
            'name'=>'syscategory_brand'
        ),//品牌搜索
    ),

    /*
    |--------------------------------------------------------------------------
    | 提供搜索分词插件
    |--------------------------------------------------------------------------
    | 对应原系统的services ID为 server.search_segment 配置
    */
    'segment' => array(
        'scws' => 'search_segment_scws',
    ),

    'segment_default' => 'scws',

    /*
    |--------------------------------------------------------------------------
    | sphinx 配置
    |--------------------------------------------------------------------------
    |
    | 对应原系统: SPHINX_SERVER_HOST SPHINX_PCONNECT
    |
    */
    'sphinx' => array(
        'host' => '127.0.0.1:9306',
        'pconnect' => true,  // 是否启用sphinx持续连接
    ),

    /*
    |--------------------------------------------------------------------------
    | scws 配置
    |--------------------------------------------------------------------------
    |
    | 对应原系统: SCWS_DICT SCWS_RULE
    | 如果是集群部署|词典路径需一致|或者词典放在同步目录里面调用
    |
    */
    'scws' => array(
        'dict' => '/usr/local/scws/etc/dict.utf8.xdb',
        'rule' => '/usr/local/scws/etc/rules.utf8.ini',
    ),
);
