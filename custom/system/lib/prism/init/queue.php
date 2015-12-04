<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class system_prism_init_queue extends system_prism_init_base
{

    public function queueCreate($appName, $queueName)
    {
        $conn = system_prism_init_util::getAppConn($appName);
        $params = [
            'topic' => $queueName,
            ];
        $this->call( $conn, '/api/platform/notify/queue/create', $params, 'get' );
        return true;
    }

    public function queueList($appName)
    {
        $conn = system_prism_init_util::getAppConn($appName);
        $queues = $this->call($conn, '/api/platform/notify/queue/list', null, 'get');
        foreach($queues as $queue)
        {
            if($queue['topic'] != '')
                $result[] = $queue['topic'];
        }
        return $result;
    }

    public function queueDrop($appName, $queueName)
    {
        $conn = system_prism_init_util::getAppConn($appName);
        $params = [
            'topic' => $queueName,
            ];
        $this->call( $conn, '/api/platform/notify/queue/drop', $params, 'get' );
        return true;
    }

    public function queueStaus($appName, $queueName)
    {
        $conn = system_prism_init_util::getAppConn($appName);
        $params = [
            'topic' => $queueName,
            ];
        return $this->call( $conn, '/api/platform/notify/status', $params, 'get' );
    }

}
