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
class base_static_cacheobject
{
    /*
     * @var boolean $_enable
     * @access static private
     */
    static private $_enable = false;

    /*
     * @var string $_instance
     * @access static private
     */
    static private $_instance = null;

    /*
     * @var string $_instance_name
     * @access static private
     */
    static private $_instance_name = null;

    /*
     * @var string $_cache_check_version_key
     * @access static private
     */
    static private $_cache_check_version_key = '__ECOS_CACHEOBJECT_CACHE_CHECK_VERSION_KEY__';

    /*
     * @var string $_cache_check_version
     * @access static private
     */
    static private $_cache_check_version = null;


    /*
     * 获取默认驱动
     * @access static public
     * @return 驱动类
     */
    static public function get_default_driver() {
        return config::get('cache.default', 'base_cache_nocache');
    }
    
    /*
     * 初始化
     * @var boolean $with_cache
     * @access static public
     * @return void
     */
    static public function init($with_cache=true)
    {
        if(config::get('cache.enabled', true) && $with_cache){
            self::$_enable = true;
            self::$_instance_name = static::get_default_driver();
        }else{
            self::$_instance_name = 'base_cache_nocache';    //todo：增加无cache类，提高无cache情况下程序的整体性能
            self::$_enable = false;
        }
        self::$_instance = null;
    }//End Function

    /*
     * 是否启用
     * @access static public
     * @return boolean
     */
    static public function enable() 
    {
        return self::$_enable;
    }//End Function

    /*
     * 获取cache_storage实例
     * @access static public
     * @return object
     */
    static public function instance()
    {
        if(is_null(self::$_instance)){
            self::$_instance = kernel::single(self::$_instance_name);
        }//使用实例时再构造实例
        return self::$_instance;
    }//End Function


    /*
     * 获取缓存
     * @var string $key
     * @var mixed &$return
     * @access static public
     * @return boolean
     */
    static public function get($key, &$return)
    {
        if(self::instance()->fetch(self::get_key($key), $data)){
            if($data['expirition'] > 0 && time() > $data['expirition']){
                return false;
            }
            $return = $data['content'];
            return true;
        }else{
            return false; 
        }
    }//End Function

    /*
     * 设置缓存
     * @var string $key
     * @var mixed $content
     * @return boolean
     */
    static public function set($key, $content, $expirition = 0)
    {
        $data['expirition'] = ($expirition>0) ? $expirition : 0;       //todo: 设置过期时间
        $data['content'] = $content;
        return self::instance()->store(self::get_key($key), $data);
    }//End Function

    /*
     * 获取缓存key
     * @var string $key
     * @access static public
     * @return string
     */
    static public function get_key($key)
    {
        $kvprefix = config::get('cache.prefix', '');
        $key_array['key'] = $key;
        $key_array['kv_prefix'] = $kvprefix;
        $key_array['prefix'] = 'cacheobject';
        $key_array['version'] = cacheobject::get_cache_check_version();
        return md5(serialize($key_array));
    }//End Function

    

    /*
     * 清空缓存 
     * todo：不是真正删除
     * 只是迭代新的缓存版本号
     * 如果使用的cache_storage不会自动释放空间，则需要人工干预
     * 也可以重截cache_storage的clean方法，实现物理删除
     * @var array &$msg
     * @access static public
     * @return boolean
     */
    static public function clean(&$msg) 
    {
        if(method_exists(self::instance(), "clean")){
            $res = self::instance()->clean();
        }else{
            $res = self::ask_cache_check_version(true);
        }
        if($res){
            foreach(kernel::servicelist('base_cacheobject_clean') AS $service){
                if(is_object($service) && method_exists($service, 'clean')){
                    $service->clean();
                }
            }
            return true;
        }else{
            return false;
        }
    }//End Function

    /*
     * 优化缓存
     * @var array &$msg
     * @access static public
     * @return boolean
     */
    static public function optimize(&$msg) 
    {
        if(method_exists(self::instance(), "optimize")){
            return self::instance()->optimize();
        }else{
            $msg = app::get('base')->_('当前缓存控制器无需优化');
            return false;
        }
    }//End Function

    /*
     * 查看缓存状态
     * @var array &$msg
     * @access static public
     * @return boolean
     */
    static public function status(&$msg) 
    {
        if(method_exists(self::instance(), "status")){
            $msg = self::instance()->status();
            return true;
        }else{
            $msg = app::get('base')->_('当前缓存控制器无法显示状态');
            return false;
        }
    }//End Function

    static public function ask_cache_check_version($force=false)
    {
        $key = self::get_cache_check_version_key();
        if($force || self::enable()){
            if($force || self::instance()->fetch($key, $val) === false){        
                $val = md5($key . time());
                self::instance()->store($key, $val);
                self::$_cache_check_version = $val; //todo：强制更新
            }
            return $val;
        }else{
            return 'static';
        }
    }//End Function

    /*
     * 取得缓存版本
     * @access static public
     * @return string
     */
    static public function get_cache_check_version() 
    {
        if(!isset(self::$_cache_check_version)){
            self::$_cache_check_version = self::ask_cache_check_version();
        }//只取一次
        return self::$_cache_check_version;
    }//End Function

    /*
     * 获得版本号的key
     * @void
     * @access static public
     * @return string
     */
    static public function get_cache_check_version_key() 
    {
        $kvprefix = config::get('cache.prefix', '');
        $key = md5($kvprefix . self::$_cache_check_version_key);
        return $key;
    }//End Function
    
    
}//end


