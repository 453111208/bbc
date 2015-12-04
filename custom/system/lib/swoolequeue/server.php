<?php
class system_swoolequeue_server implements system_interface_swoolequeue_ICallback{

    /**
	 * 当socket服务启动时，回调此方法
	 */
    public function onStart($server)
    {
        swoole_set_process_name("php swoole master");
    }

    /**
	 * 当有数据到达时，回调此方法
	 */
    public function onReceive($server, $fd, $fromId, $data)
    {
        $getdata = trim($data);
        switch($getdata)
        {
        case "reload":
            $server->reload();
            break;
        case "start":
            $this->_addtask($server);
            $server->addtimer(10000);
            break;
        case "shutdown":
            $server->shutdown();
            break;
        }
    }

    public function onConnect(swoole_server $server, $fd, $fromId)
    {
        $log_file = $server->setting['log_file'];
        $pid_file = str_replace('.log',".pid",$log_file);

        if(file_exists($pid_file))
        {
            $fp = fopen("$pid_file", "r");
            $re = fread($fd,filesize($pid_file));
        }
        else
        {
            //$masterPid = $server->master_pid;
            $masterPid = $server->manager_pid;
            $fp = fopen("$pid_file", "w+");
            $re = fwrite($fp,$masterPid);
        }

        if($re)
        {
            //$this->_addtask($server);
            $server->addtimer(10000);
        }
    }

    /**
     * 当启用addtimer()时，回调此方法
     */
    public function onTimer($server, $intsec)
    {
        echo 2;
        $taskstats = $server->stats();
       echo "\n"; print_r($taskstats);echo "\n";
        if($taskstats['tasking_num'] == 0)
        {
            $this->_addtask($server);
        }
    }

    /**
     * 当调用task()时，回调此方法
     */
    public function onTask($server, $taskId, $fromId, $data)
    {
        echo 3;
        $getdata = unserialize(trim($data));
        $worker = $getdata['worker'];
        $params = $getdata['params'];
        $obj_task = new $worker;
        if ($obj_task instanceof base_interface_task)
        {
            call_user_func_array(array($obj_task, 'exec'), array($params));
        }
        return $data;
    }

    /**
     * 当task进程通过finishi() 或者 return string 将任务处理的结果发送给worker进程时，回调此方法
     */
    public function onFinish($server, $taskId, $data)
    {
        echo 4;
        $result = unserialize($data);
        return system_queue::instance()->ack($result['id']);
    }

    /**
     * worker进程/task进程启动时,回调此方法
     */
    public function onWorkerStart($server, $worderId)
    {
        $workerPid = $server->worker_pid;
        //app::get('system')->setConf($this->queue.'_worker_pid',$workerPid);
        if($workerId >= $server->setting['worker_num'])
        {
            swoole_set_process_name("php swoole task_worker");
        }
        else
        {
            swoole_set_process_name("php swoole event_worker");
        }
    }

     /**
     * worker进程终止时,回调此方法
     */
    public function onWorkerStop($server, $worderId)
    {
    }

    private function _addtask($server)
    {
        $queuelist = system_queue::getList();
        if($queuelist)
        {
            foreach($queuelist as $key=>$value)
            {
                $mess = serialize($value);
                $server->task($mess);
            }
        }
    }
}
