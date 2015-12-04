<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 

class base_status_system extends base_status_abstract{
    
    function get_cache_status(){
        $driver = config::get('cache.default', 'base_cache_secache');
        $ret = array(
            'cache.engine'=>$driver,
            );
            
        if(method_exists($driver,'status')){
            foreach(kernel::single($driver) as $k=>$v){
                $ret['cache.'.$k] = $v;
            }
        }
        return $ret;
    }
    
    function get_kvstore_status(){
        $driver = config::get('kvstore.default', 'base_kvstore_filesystem');
        $ret = array(
            'kvstore.engine'=>$driver,
            );
            
        if(method_exists($driver,'status')){
            foreach(kernel::single($driver) as $k=>$v){
                $ret['kvstore.'.$k] = $v;
            }
        }
        return $ret;
    }
    
    function get_mysql_status(){
        $username = config::get('database.username');
        
        $host = config::get('database.host');

        $database = config::get('database.database');
        
        $aResult = array(
            'mysql.server_host'=>$host,
            'mysql.server_dbname'=>$database,
            'mysql.server_user'=>$username,
        );
        // 需要补充逻辑
        
    }

}
