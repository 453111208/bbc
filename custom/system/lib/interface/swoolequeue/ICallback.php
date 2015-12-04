<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 *
 * @author ajx
 */

interface system_interface_swoolequeue_ICallback{

    /**
	 * 当socket服务启动时，回调此方法
	 */
    public function onStart($server);

    /**
	 * 当有数据到达时，回调此方法
	 */
    public function onReceive($server, $fd, $fromId, $data);

    /**
     * 当启用addtimer()时，回调此方法
     */
    public function onTimer($server, $intsec);

    /**
     * 当调用task()时，回调此方法
     */
    public function onTask($server, $taskId, $fromId, $data);

    /**
     * 当task进程通过finishi() 或者 return string 将任务处理的结果发送给worker进程时，回调此方法
     */
    public function onFinish($server, $taskId, $data);

    /**
     * worker进程/task进程启动时,回调此方法
     */
    public function onWorkerStart($server, $worderId);

     /**
     * worker进程终止时,回调此方法
     */
    public function onWorkerStop($server, $worderId);
}


