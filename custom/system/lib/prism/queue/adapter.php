<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class system_prism_queue_adapter implements system_interface_queue_adapter
{

    static private $__connection = array();

    public function publish($queueName, $queueData)
    {
        $time = time();
        $data = array(
            'queue_name' => $queueData['queue_name'],
            'worker' => $queueData['worker'],
            'params' => (array)$queueData['params'],
            'create_time' => $time,
        );
        $notifyMessage = json_encode($data);
        $conn = $this->__get_connection($queueName);
        $res = $conn->post('/platform/notify/write', array('topic'=>$queueName, 'data'=>$notifyMessage));
        $res = json_decode($res, 1);
        return $res['result'];

    }

    public function publishByRoutingKey($routingKey, $queueData)
    {
        $time = time();
        $data = array(
            'queue_name' => $queue_data['queue_name'],
            'worker' => $queue_data['worker'],
            'params' => (array)$queue_data['params'],
            'create_time' => $time,
        );
        $notifyMessage = json_encode($data);
        $conn = $this->__get_connection($queueName);
        return $conn->publish($routingKey, $notifyMessage);
    }

    public function consume($queueName)
    {
        $conn = $this->__get_connection($queueName);
        $res = $conn->consume($queueNqme);
        $res = json_decode($res);
        $notifyMessage = $res->body;
        $data = json_decode($notifyMessage, 1);
        $tag = $res->tag;
        $messageData = array(
            'tag' => $tag,
            'worker' => $data['worker'],
            'params' => $data['params'],
            'queueNqme' => $data['queueNqme'],
        );
        return new system_prism_queue_message($messageData);
    }

    public function get($queueNqme)
    {
        $conn = $this->__get_connection($queueName);
        $res = $conn->get('/api/platform/notify/read', array('num'=>1, 'topic'=>$queueName));
        $res = json_decode($res, 1);
        $messageData = json_decode($res['result'][0]);
        $data = array(
            'tag'=>null,
            'worker'=>$messageData['worker'],
            'params'=>$messageData['params'],
            'queueName'=>$messageData['queue_name'],
        );
        return new system_prism_queue_message($data);
    }

    public function purge($queueName)
    {
        $conn = $this->__get_connection($queueName);
        $conn->post('/api/platform/notify/purge', array('topic'=>$queueName));
    }

    public function ack($queue_message)
    {
        $conn = $this->__get_connection($queue_message->get_queueName());
        $conn->ack($queue_message->get_tag());
    }

    public function is_end($queueName)
    {
        $conn = $this->__get_connection($queueName);
        $res = $conn->get('/api/platform/notify/status', array('topic'=>$queueName));
        $res = json_decode($res, 1);
        if($res['result']['Backing_queue_status']['Len'] == 0)
        {
            return true;
        }
        else
        {
            return false;
        }

    }

    private function __get_connection($queueName)
    {
        if(!isset(self::$__connection[$queueName]))
        {
            //获取这个队列是哪个app的，就可以根据appId去获取推送队列时用的key和secret
            $queueConf = config::get('queue.queues');
            $queueC = $queueConf[$queueName];
            $appId = $queueC['app'];

            //接下来就是要把app转化成key和secret了
            $keySecret = apiUtil::getPrismKey($appId);
            $key = $keySecret['key'];
            $secret = $keySecret['secret'];

            $host = config::get('prism.prismHostUrl');
            $host = rtrim($host, '/') . '/api';

            //这个是长连接的配置
            $socketFile = config::get('prism.prismSocketFile');

            //创建队列传输链接
            self::$__connection[$queueName] = new base_prism_client($host, $key, $secret, $socketFile);
        }
        return self::$__connection[$queueName];
    }

}
