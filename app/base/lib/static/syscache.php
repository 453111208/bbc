<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class base_static_syscache
{
    static private $__supports = array(
        'service' => 'base_syscache_service',
        'setting' => 'base_syscache_setting');

    static private $__instance = array();

    private $_controller = null;

    private $_support_type = null;

    private $_handler = null;

    static public function instance($support_type){
        if (!isset(self::$__supports[$support_type])) return false;
        if (!isset(self::$__instance[$support_type])) {
            self::$__instance[$support_type] = new syscache($support_type);
            
        }
        return self::$__instance[$support_type];
    }

    
    public function __construct($support_type){
        $this->_support_type = $support_type;

        $this->_handler = new self::$__supports[$support_type];
        if ($this->_handler instanceof base_interface_syscache_farmer) {
            if (defined('SYSCACHE_ADAPTER')) {
                $class_name = constant('SYSCACHE_ADAPTER');
            }else{
                $class_name = 'base_syscache_adapter_filesystem';
            }
            $this->set_controller(new $class_name($this->_handler));
            if ($this->get_controller()->init()!==true)
            {
                $this->_reload();
            }
            return true;
        } else {
            throw new RuntimeException('this instance must implements base_interface_farmer');
        }
        
    }

    public function _reload(){
        $this->get_controller()->create($this->_handler->get_data());
        $this->get_controller()->init();
    }

    public function set_controller($controller){
        if($controller instanceof base_interface_syscache_adapter){
            $this->_controller = $controller;
        }else{
            throw new RuntimeException('this instance must implements base_interface_syscache_adapter');
        }
        
    }

    public function get_controller(){
        return $this->_controller;
    }

    public function set_last_modify(){
        $this->_handler->set_last_modify();
        $this->_reload();
    }

    public function get($key){
        return $this->_controller->get($key);
    }
}