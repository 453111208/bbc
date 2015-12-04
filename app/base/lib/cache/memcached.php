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
 */
class base_cache_memcached extends base_cache_abstract implements base_interface_cache
{
    static private $_cacheObj = null;

    function __construct() 
    {
        $this->connect();
    }//End Function

    public function connect() 
    {
        if(!isset(self::$_cacheObj)){
            $hosts = (array)config::get('cache.base_cache_memcached.hosts');
            if (!empty($hosts)) {
                self::$_cacheObj = new Memcached;
                foreach($hosts AS $row){
                    $row = trim($row);
                    if(strpos($row, 'unix://') === 0){
                        //self::$_cacheObj->addServer($row, 0);  todo:memcached不支持unix://
                    }else{
                        $tmp = explode(':', $row);
                        self::$_cacheObj->addServer($tmp[0], $tmp[1]);
                    }
                }
            }else{
                trigger_error('cache:base_cache_memcached hosts is empty, please check it', E_USER_ERROR);
            }
        }
    }//End Function

    public function fetch($key, &$result) 
    {
        $result = self::$_cacheObj->get($key);
        if(self::$_cacheObj->getResultCode() == Memcached::RES_NOTFOUND){
            return false;
        }else{
            return true;
        }
    }//End Function

    public function store($key, $value) 
    {
        return self::$_cacheObj->set($key, $value);
    }//End Function

    public function status() 
    {
        $status = self::$_cacheObj->getStats();
        foreach($status AS $key=>$value){
            $return['服务器']   = $key;
            $return['缓存获取'] = $value['cmd_get'];
            $return['缓存存储'] = $value['cmd_set'];
            $return['可使用缓存'] = $value['limit_maxbytes'];
        }
        return $return;
    }//End Function

    public function clean() {
        return self::$_cacheObj->flush();
    }
    
}//End Class
