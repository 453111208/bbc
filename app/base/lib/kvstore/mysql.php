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
class base_kvstore_mysql extends base_kvstore_abstract implements base_interface_kvstore_base
{

    function __construct($prefix) 
    {
        $this->prefix = $prefix;
    }//End Function

    public function store($key, $value, $ttl=0) 
    {
        $rows = app::get('base')->model('kvstore')->getList('id', array('prefix'=>$this->prefix, 'key'=>$key));
        $data = array('prefix'=>$this->prefix, 'key'=>$key, 'value'=>$value, 'dateline'=>time(), 'ttl'=>$ttl);
        if($rows[0]['id'] > 0){
            return app::get('base')->model('kvstore')->update($data, array('id'=>$rows[0]['id']));
        }else{
            return app::get('base')->model('kvstore')->insert($data);
        }
    }//End Function

    public function fetch($key, &$value, $timeout_version=null) 
    {
        $rows = app::get('base')->database()->executeQuery('SELECT * FROM `base_kvstore` WHERE `prefix` = ? AND `key` = ?', [$this->prefix, $key])->fetchAll();

        if($rows[0]['id'] > 0 && $timeout_version < $rows[0]['dateline']){
            if($rows[0]['ttl'] > 0 && ($rows[0]['dateline']+$rows[0]['ttl']) < time()){
                return false;
            }
            $value = unserialize($rows[0]['value']);
            return true;
        }
        return false;
    }//End Function

    public function delete($key) 
    {
        return app::get('base')->model('kvstore')->delete(array('prefix'=>$this->prefix, 'key'=>$key));
    }//End Function

    public function recovery($record) 
    {
        return false;
    }//End Function
}//End Class
