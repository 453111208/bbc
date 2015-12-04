<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_rpc_service{

    private $start_time;
    private $path = array();
    private $finish = false;
    static $node_id;
    static $api_info;
    static public $is_start = false;

    function __construct(){
        if(!kernel::is_online()){
            die('error');
        }

        cacheobject::init();
        self::$is_start = true;
    }

    public function process($path){

        if(strpos($path, '/openapi') !== false){
            $args = explode('/',substr($path,9));
            $service_name = 'openapi.'.array_shift($args);
            $method = array_shift($args);
            foreach($args as $i=>$v){
                if($i%2){
                    $params[$k] = str_replace('%2F','/',$v);
                }else{
                    $k = $v;
                }
            }
            kernel::service($service_name)->$method($params);
		}
    }
}
