<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 

/*
 * @package base
 * @copyright Copyright (c) 2010, shopex. inc
 * @author edwin.lzh@gmail.com
 * @license 
 * 为了数据安全，请确保persistent方法的调用正确
 */
class base_kvstore{

    /*
     * @var string $__instance
     * @access static private
     */
    static private $__instance = array();

    /*
     * @var string $__persistent
     * @access static private
     */
    static private $__persistent = true;

    /*
     * @var string $__controller
     * @access private
     */
    private $__controller = null;

    /*
     * @var string $__prefix
     * @access private
     */
    private $__prefix = null;
    
    /*
     * @var string $__fetch_count
     * @access static public
     */
    static public $__fetch_count = 0;

    /*
     * @var string $__store_count
     * @access static public
     */
    static public $__store_count = 0;

    /*
     * 获取默认驱动
     * @access static public
     * @return 驱动类
     */
    static public function get_default_driver() {
        return config::get('kvstore.default', 'base_kvstore_filesystem');
    }
    
    /*
     * 构造
     * @var string $prefix
     * @access public
     * @return void
     */
    function __construct($prefix){
        $driver = static::get_default_driver();
        $this->set_controller(kernel::single($driver, $prefix));
        $this->set_prefix($prefix);
    }//End Function

    /*
     * 设置持久化与否
     * @var boolean $flag
     * @access public
     * @return string
     */
    static function config_persistent($flag) 
    {
        self::$__persistent = ($flag) ? true : false;
    }//End Function

    /*
     * 返回KV PREFIX
     * @access public
     * @return string
     */
    static public function kvprefix() 
    {
        $prefix = config::get('kvstore.prefix');
        return $prefix ?: 'bbc-';
    }//End Function

    /*
     * 实例一个kvstore
     * @var string $prefix
     * @access public
     * @return object
     */
    static public function instance($prefix){
        if(!isset(self::$__instance[$prefix])){
            self::$__instance[$prefix] = new base_kvstore($prefix);
        }
        return self::$__instance[$prefix];
    }//End Function

    /*
     * 设置prefix
     * @var string $prefix
     * @access public
     * @return void
     */
    public function set_prefix($prefix) 
    {
        $this->__prefix = $prefix;
    }//End Function

    /*
     * 取得prefix
     * @access public
     * @return string
     */
    public function get_prefix() 
    {
        return $this->__prefix;
    }//End Function

    /*
     * 设置kvstore控制器
     * @var object $controller
     * @access public
     * @return void
     */
    public function set_controller($controller) 
    {
        if($controller instanceof base_interface_kvstore_base){
            $this->__controller = $controller;
        }else{
            throw new \RuntimeException('this instance must implements base_interface_kvstore_base');
        }
    }//End Function

    /*
     * 得到kvstore控制器
     * @access public
     * @return object
     */
    public function get_controller() 
    {
        return $this->__controller;
    }//End Function

    /*
     * 自增
     * @var string $key
     * @var int $offset
     * @access public
     * @return int
     */
    public function increment($key, $offset=1) 
    {
        if($this->get_controller() instanceof base_interface_kvstore_extension){
            return $this->get_controller()->increment($key, $offset);
        }else{
            throw new \RuntimeException('this instance can\'t support increment');
        }
    }//End Function

    /*
     * 自减
     * @var string $key
     * @var int $offset
     * @access public
     * @return int
     */
    public function decrement($key, $offset=1) 
    {
        if($this->get_controller() instanceof base_interface_kvstore_extension){
            return $this->get_controller()->decrement($key, $offset);
        }else{
            throw new \RuntimeException('this instance can\'t support decrement');
        }
    }//End Function

    /*
     * 获取key的内容
     * @var string $key
     * @var mixed &$value
     * @var int $timeout_version
     * @access public
     * @return boolean
     */
    public function fetch($key, &$value, $timeout_version=null){
        self::$__fetch_count++;
        logger::debug('kvstore:'.self::$__fetch_count.'.'.' instance:'.$this->get_prefix().' fetch key:'.$key);
        if($this->get_controller()->fetch($key, $value, $timeout_version)){
            return true;
        }else{
            return false;
        }
    }//End Function

    /*
     * 设置key的内容
     * @var string $key
     * @var mixed $value
     * @var int $ttl
     * @access public
     * @return boolean
     */
    public function store($key, $value, $ttl=0)
    {
        self::$__store_count++;
        $persistent = config::get('kvstore.persistent', true);
        if($persistent && self::$__persistent && get_class($this->get_controller())!='base_kvstore_mysql' && kernel::is_online()){
            $this->persistent($key, $value, $ttl);
        }
        logger::debug('kvstore:'.self::$__fetch_count.'.'.' instance:'.$this->get_prefix().' store key:'.$key);
        return $this->get_controller()->store($key, $value, $ttl);
    }//End Function

    /*
     * 删除key的内容
     * @var string $key
     * @var int $ttl
     * @access public
     * @return boolean
     */
    public function delete($key, $ttl=1) 
    {
        if($this->fetch($key, $value)){
            return $this->store($key, $value, ($ttl>0)?$ttl:1);    //todo: 不实际删除，由cron统一处理delete
        }
        return true;
    }//End Function

    /*
     * 数据持久化
     * @var string $key
     * @var mixed $value
     * @var int $ttl
     * @access public
     * @return void
     */
    public function persistent($key, $value, $ttl=0) 
    {
        kernel::single('base_kvstore_mysql', $this->get_prefix())->store($key, $value, $ttl);  //todo: 持久化
    }//End Function
    
    /*
     * 数据还原
     * @var array $record
     * @access public
     * @return boolean
     */
    public function recovery($record) 
    {
        return $this->get_controller()->recovery($record);
    }//End Function

    /*
     * 删除过期数据
     * @var array $record
     * @access public
     * @return boolean
     */
    static public function delete_expire_data() 
    {
        $time = time();
        $db = app::get('base')->database();
        $count = $db->executeQuery('SELECT count(*) FROM base_kvstore WHERE ttl>0 AND (dateline+ttl)<?',
                            [$db->quote($time, \PDO::PARAM_INT)])->fetchColumn();
        
        $pagesize = 100;
        $page = ceil($count / 100);
        for($i=0; $i<$page; $i++){
            $rows = app::get('base')->database()->executeQuery('SELECT `prefix`, `key` FROM base_kvstore WHERE ttl>0 AND (dateline+ttl)<? limit ? offset ?', [$time, $pagesize, $pagesize*$i])->fetchAll();
            
            foreach($rows AS $row)
            {
                $single = base_kvstore::instance($row['prefix']);
                if(get_class($single->get_controller()) != 'base_kvstore_mysql'){
                    $single->get_controller()->delete($row['key']);
                }
            }
        }
        $db->executeUpdate('DELETE FROM base_kvstore WHERE ttl>0 AND (dateline+ttl)<?', [$time]);
    }//End Function

}//End Class
