<?php
class system_runswoole{

    public function run($cmd="",$queue=null)
    {
        switch($cmd)
        {
        case "start":
            $this->_start($queue);
            break;
        case "restart":
            $this->_restart($queue);
            break;
        case "reload":
            $this->_reload($queue);
            break;
        case "shutdown":
            $this->_shutdown($queue);
            break;
        case "client":
            $this->_client($queue);
            break;
        }
    }

    private function _client($queue)
    {
        $objSwoole = kernel::single('system_swoolequeue_swoole');
        return $objSwoole->clientrun($queue);
    }

    /**
        * @brief 启动swoole server
        *
        * @param $queue
        *
        * @return
     */
    private function _start($queue)
    {

        $objSwoole = kernel::single('system_swoolequeue_swoole');
        if(!$pid = $this->_pidfile())
        {
            $objSwoole->serverrun($queue);
        }
        return true;
    }

    /**
     * @brief swoole server 重启
     *
     * @param string $queue 具体队列
     *
     * @return
     */
    private function _restart($queue)
    {
        $result = $this->_shutdown($queue);
        if($result)
        {
            $result = $this->_start($queue);
        }
        return $result;
    }

    /**
     * @brief swoole server 热启动（）
     *
     * @param $queue
     *
     * @return
     */
    private function _reload($queue)
    {
    }

    /**
     * @brief 结束swoole server
     *
     * @param $queue
     *
     * @return
     */
    private function _shutdown($queue)
    {
        $pid = $this->_pidfile();
        echo $pid;
        $pid_file = DATA_DIR."/swoole.pid";
        system('kill -15 `cat '.$pid_file.'`');
        return unlink($pid_file);
    }

    private function _pidfile()
    {
        $pid_file = DATA_DIR."/swoole.pid";
        if(file_exists($pid_file))
        {
            $fp = fopen("$pid_file", "r");
            $pid = fread($fd,filesize($pid_file));
            return $pid;
        }
        return false;
    }
}
