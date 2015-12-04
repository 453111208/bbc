<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class system_queue{

    static private $__instance = null;

    static private $__config = null;

    private $__controller = null;

    static private function __init() {
        if (!isset(self::$__config)) {
            self::$__config['queues'] = (array)config::get('queue.queues', array());
            self::$__config['bindings'] = (array)config::get('queue.bindings', array());
            self::$__config['worker'] = (array)config::get('queue.worker', array());
            self::$__config['action'] = (array)config::get('queue.action', array());
        }
    }

    static public function get_config($key=null){
        if (!is_null($key)) {
            return self::$__config[$key];
        }
        return self::$__config;

    }

    public function __construct(){
        self::__init();
        $controller = self::get_driver_name();
        $this->set_controller(new $controller);
    }

    static public function get_driver_name(){
        return config::get('queue.default', 'system_queue_adapter_mysql');
    }

    public function get_controller(){
        return $this->__controller;
    }

    public function set_controller($controller){
        if ($controller instanceof system_interface_queue_adapter) {
            $this->__controller = $controller;
        }else{
            throw new Exception('this instance must implements system_interface_queue_adapter');
        }
    }

    static public function get_queue($queue_name){
        if (isset(self::$__config['queues'][$queue_name])) {
            return self::$__config['queues'][$queue_name];
        }
        return false;
    }

    static public function get_exchange($exchange_name){
        if (isset(self::$__config['exchanges'][$exchange_name])) {
            return self::$__config['exchanges'][$exchange_name];
        }
        return false;
    }

    static public function get_queues() {
        return self::$__config['queues'];
    }

    static public function get_bindings(){
        return self::$__config['bindings'];
    }

    static public function instance(){
        if (!isset(self::$__instance)) {
            self::$__instance = new system_queue;
        }
        return self::$__instance;
    }

    static private function __get_publish_queues($exchange_name){
        if (!isset(self::$__config['bindings'][$exchange_name])){
            $default_publish_queue = config::get('queue.default_publish_queue');
            return array($default_publish_queue);
        }
        return self::$__config['bindings'][$exchange_name];
    }

    static public function __get_push_queue($worker)
    {
        return self::$__config['worker'][$worker]['queue'];
    }

    static public function __get_worker_class($worker)
    {
        return self::$__config['worker'][$worker]['class'];
    }

    static public function __get_push_workers($action)
    {
        $action = (array)config::get('queue.action', array());
        return $action[$action];
    }

    public function publish($exchange_name, $worker, $params=array(), $routing_key=null){
        $queues = $this->__get_publish_queues($exchange_name);
        foreach($queues as $queue_name){
            $queue_data = array(
                'queue_name' => $queue_name,
                'worker' => $worker,
                'params' => $params,
            );
            $this->get_controller()->publish($queue_name, $queue_data);
        }
        return true;
    }

    static public function push($worker, $params)
    {
        $instance = self::instance();
        $queue_data = array(
            'queue_name' => self::__get_push_queue($worker),
            'worker' => self::__get_worker_class($worker),
            'params' => $params,
        );
        $queue_name = self::__get_push_queue($worker);
        logger::info('queue push : ' . var_export($queue_data));
        $instance->get_controller()->publish($queue_name, $queue_data);
        return true;
    }

    static public function action($action, $params)
    {
        $workers = self::__get_push_workers($action);
        self::bulk($workers, $params);
        return true;
    }

    static public function bulk($workers, $params)
    {
        foreach ($workers as $worker)
        {
            self::push($worker, $params);
        }
        return true;
    }

    public function get($queue_name){
        $queue_message = $this->get_controller()->get($queue_name);
        if ($queue_message instanceof system_interface_queue_message) {
            return $queue_message;
        }
        return false;
    }

    public function getList()
    {
       return $list = app::get('system')->model('queue_mysql')->getList("*","",0,10);
       return $list[0];
    }

    public function ack($queue_message){
        $this->get_controller()->ack($queue_message);
    }

    public function delete($queue_message)
    {
        return app::get('system')->model('queue_mysql')->delete(array('id'=>$queue_message));
    }


    public function run_task($queue_message){
        //todo: 异常处理
        $worker = $queue_message->get_worker();
        $params = $queue_message->get_params();

        $obj_task = new $worker();
        if ($obj_task instanceof base_interface_task) {
            call_user_func_array(array($obj_task, 'exec'), array($params));
            logger::info('task:'. get_class($obj_task). ' exec ok');
        }
        return true;
    }

    public function purge($queue_name){
        $this->get_controller()->purge();
    }

    public function is_end($queue_name){
        return $this->get_controller()->is_end($queue_name);
    }
}
