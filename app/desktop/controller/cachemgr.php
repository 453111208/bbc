<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 

class desktop_ctl_cachemgr extends desktop_controller 
{
    
    public function index() 
    {
        $pagedata['enable'] =
        (get_class(cacheobject::instance()) == 'base_cache_nocache') ? false : true;
         if(cacheobject::status($msg)){
           $pagedata['status']  = $msg; 
        }
         $pagedata['static_cache'] = array();
         foreach( kernel::servicelist('site.router.cache') as $value) {
             if(!method_exists($value, 'get_cache_methods'))  continue;
             $methods = $value->get_cache_methods();
             foreach( (array) $methods as $method ) {
                 if(isset($method['app']) && isset($method['ctl']) && isset($method['act'] )) {
                     if($expires = app::get('site')->getConf($method['app'] . '_' . $method['ctl'] . '_' .$method['act'] . '.cache_expires')) {
                         $method['expires'] = $expires;
                     }
                     $pagedata['static_cache'][] = $method;
                 }
             }
         }   
         return $this->page('desktop/cachemgr/index.html', $pagedata);
    }//End Function

    public function status() 
    {  
        
        $this->index();
    }

    public function optimize() 
    {
    
        $this->begin('');
        $this->end(cacheobject::optimize($msg),$msg);
    }//End Function
    
    public function clean() 
    {
        $this->begin('');
        $this->end(cacheobject::clean($msg),$msg);
    }//End Function

    function save() {
        $expirse = $_POST;
        $key = key($_POST);
        $value = current($_POST);
        app::get('site')->setConf($key.'.cache_expires', (int)$value);
    }
}//End Class
