<?php
class system_swoolequeue_swoole{

    public $config;
    public $host;
    public $port;
    public $objCallback;

    private function _setting($queue=null)
    {
        $q = array();
        $config = config::get('swoolequeue.config');
        $type = config::get('swoolequeue.type');
        if($queue){
            $q = $type[$queue];
            if($q['worker_num'])  $config['worker_num'] = $q['worker_num'];
            if($q['task_worker_num'])  $config['task_worker_num'] = $q['task_worker_num'];
            if($q['reactor_num'])  $config['reactor_num'] = $q['reactor_num'];
        }

        if($config['log_file']) $config['log_file'] = DATA_DIR."/".$config['log_file'];
        $this->host = $q['host'] ? $q['host'] : $config['host'];
        $this->port = $q['port'] ? $q['port'] : $config['port'];
        $this->mode = $q['mode'] ? $q['mode'] : $config['mode'];

        $callbackClass = $q['callback_class'] ? $q['callback_class'] : $config['callback_class'];

        unset($config['host'],$config['port'],$config['mode']);
        if(!$config)
        {
            throw new \LogicException("配置项参数缺失");
        }
        $this->config = $config;

        if(!$callbackClass)
        {
            throw new \LogicException("配置项参数缺失");
        }

        $this->objCallback = new $callbackClass();
        return true;
    }

    public function serverrun($queue)
    {
        try{
            $this->_setting($queue);
        }
        catch(Exception $e)
        {
            throw new \LogicException($e->getMessage());
        }

        $serv = new swoole_server($this->host, $this->port, $this->mode);
        $serv->set($this->config);
        $serv->on('Start', array($this->objCallback, 'onStart'));
        $serv->on('Receive', array($this->objCallback, 'onReceive'));
        $serv->on('Connect', array($this->objCallback, 'onConnect'));
        $serv->on('Timer', array($this->objCallback, 'onTimer'));
        $serv->on('Task', array($this->objCallback, 'onTask'));
        $serv->on('Finish', array($this->objCallback, 'onFinish'));
        $serv->on('WorkerStart', array($this->objCallback, 'onWorkerStart'));
        $serv->on('WorkerStop', array($this->objCallback, 'onWorkerStop'));

        $onArray = array(
            'onShutdown',
            'onClose',
            'onPipeMessage',
            'onManagerStart',
            'onManagerStop',
            'onWorkerError',
        );

        foreach($onArray as $on) {
            if(method_exists($this->objCallback, $on)) {
                $serv->on(str_replace('on', '', $on), array($this->objCallback, $on));
            }
        }
        $serv->start();
    }

    public function clientrun($queue=null)
    {
        try{
            $this->_setting($queue);
        }
        catch(Exception $e)
        {
            throw new \LogicException($e->getMessage());
        }

        $cli = new swoole_client(SWOOLE_SOCK_TCP,SWOOLE_SOCK_SYNC); //异步
        if(!$cli->connect($this->host,$this->port))
        {
            return false;
            exit("connect failed. Error: {$client->errCode}\n");
        }
        $cli->close();
        return true;
    }
}

