<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 *
 * @author ajx
 */

interface system_interface_swoolequeue_SCallback{

	/**
	 * 当socket服务启动时，回调此方法
	 */
    public function onStart($server);

    /**
	 * 当有client连接上socket服务时，回调此方法
	 */
    public function onConnect($server, $fd, $fromId);

    /**
	 * 当有数据到达时，回调此方法
	 */
    public function onReceive($server, $fd, $fromId, $data);

    /**
	 * 当有client断开时，回调此方法
	 */
    public function onClose($server, $fd, $fromId);

    /**
     * 当manager 进程启动时，回调此方法
     */
    public function onManagerStart($server);

    /**
     * 当manager 进程结束时，回调此方法
     */
    public function onManagerStop($server);
}
