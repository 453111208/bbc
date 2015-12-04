<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class base_vcode
{

    function __construct(){
        $this->obj = kernel::single('base_vcode_gd');
        kernel::single('base_session')->start();
    }

    function length($len) {
        $this->obj->length($len);
        return true;
    }

    public function setPicSize($height=35, $width=100)
    {
        $this->obj->setPicSize($height, $width);
        return true;
    }

    function verify_key($key){
        $sess_id = kernel::single('base_session')->sess_id();
        $key = $key.$sess_id;
        $ttl = 180;
        if(config::get('cache.enabled', true)){
            cacheobject::set($key,$this->obj->get_code(),$ttl+time());
        }else{
            base_kvstore::instance('vcode')->store($key,$this->obj->get_code(),$ttl);
        }
    }

    static function verify($key,$value){
        $value = strtolower($value);
        $sess_id = kernel::single('base_session')->sess_id();
        $vcodekey = $key.$sess_id;
        if(config::get('cache.enabled', true)){
            cacheobject::get($vcodekey,$vcode);
            //使用后则是过期
            cacheobject::set($vcodekey,$vcode,time()-1);
        }else{
            base_kvstore::instance('vcode')->fetch($vcodekey,$vcode);
            //使用后则是过期
            base_kvstore::instance('vcode')->store($vcodekey,$vcode,1);
        }

        if( $vcode == strval($value) )
        {
            return true;
        }

        return false;
    }

    function display(){
        $this->obj->display();
    }
}
