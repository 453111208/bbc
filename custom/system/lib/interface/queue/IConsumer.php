<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 *
 * @author hjx
 */

interface system_interface_queue_IConsumer{
    
    /**
     * 设置子进程最大可执行时间，单位：秒
     * 
     * @param int $sec
     */
    public function setMaxExecTime($sec);
    
    /**
     * 获取子进程最大可执行时间，单位：秒
     *
     * @return int $sec
     */
    public function getMaxExecTime();
    
    /**
     * 执行具体的任务
     *
     * @param string $queue_name 队列名称
     * @param int $max 最大可开启的进程数
     */
    public function exec($queue_name,$max);
}
