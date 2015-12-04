<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

return array(

    'config' => array(
        /*
        |--------------------------------------------------------------------------
        | swoole 启动时的基础配置
        | swoole server config
        |--------------------------------------------------------------------------
         */

        'host' => '127.0.0.1', //socket 监听ip
        'port' => '9507', //socket 监听端口
        'mode' => SWOOLE_PROCESS, //运行的模式 1.Base模式 2.线程模式 3.进程模式 默认多进程模式
        //'mode' => 3,
        'worker_num' => 3,   //worker进程数
        'task_worker_num' => 3,   //task进程的数量
        'daemonize' => 1, //是否开启守护进程
        'log_file' => "swoole.log", //swoole错误日志文件
        //'max_request' => 10000,   //worker进程的最大任务数
        //'reactor_num' => 8,  //reactor线程数
        //'max_conn' => 10000,  //最大连接数
        //'dispatch_mode' => 2, //数据包分发策略 1.轮循模式 2.固定模式 3.枪战模式 4.ip分配 5.uid分配 默认2
        //'open_length_check' => true,  //打开包长检测特性
        //'backlog' => 128, //listen队列长度
        'callback_class' => 'system_swoolequeue_server',
    ),

    'type' => array(
        'quick' => array(
            'title' => '快队列',
            'task_worker_num' => 5,   //task进程的数量
            'worker_num' => 5,   //worker进程数
            'host' => '0.0.0.0', //socket 监听ip
            'port' => 8881, //socket 监听端口
            'callback_class' => 'system_swoolequeue_server',
        ),
        'slow' => array(
            'title' => '慢队列',
            'worker_num' => 3,   //worker进程数
            'task_worker_num' => 3,   //task进程的数量
            'host' => '0.0.0.0', //socket 监听ip
            'port' => 8882, //socket 监听端口
            'callback_class' => 'system_swoolequeue_server',
        ),
        'normal' => array(
            'title' => '其他队列',
            'task_worker_num' => 3,   //task进程的数量
            'worker_num' => 3,   //worker进程数
            'host' => '0.0.0.0', //socket 监听ip
            'port' => 8883, //socket 监听端口
            'callback_class' => 'system_swoolequeue_server',
        ),
    ),
);

