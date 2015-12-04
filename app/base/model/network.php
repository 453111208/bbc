<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 

class base_mdl_network extends dbeav_model{
    // matrix async api flag
    const MATRIX_ASYNC = 1;
    // matrix sync api flag
    const MATRIX_REALTIME = 2;
    // matrix service api flag
    const MATRIX_SERVICE = 3;

    function call(){
        if(!$this->json_rpc){
            $this->json_rpc = $this->app->load('json_rpc');
        }
        $args = func_get_args();
        $url = array_shift($args);
        $func = array_shift($args);
        array_unshift($args,$this->system->base_url());
        return $this->json_rpc->call($url,$func,$args);
    }
  
}
