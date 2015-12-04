<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_storage_ttprosystem implements base_interface_storager {

    public function base_storage_ttprosystem()
    {
        $this->memcache = new Memcached;
        $hosts = (array)config::get('storager.ttprosystem.hosts');
        if(!empty($hosts))
        {
            foreach($hosts as $k =>$v)
            {
                list($host,$port) = explode(":",$v);
                $this->memcache->addServer($host,$port);
            }
        }

        $this->mediaDirectory = str_replace(PUBLIC_DIR,'',MEDIA_DIR);
    }

    public function save( $fileObject )
    {
        $result = $this->__getIdent( $fileObject );
        if( !$result['ident'] ) return false;

        if( !$this->memcache->set($result['ident'],file_get_contents($fileObject->getPathname()),0) )
        {
            return false;
        }

        $data['ident'] = $result['ident'];
        $data['url'] = $result['ident'];
        return $data;
    }

    public function rebuild($fileObject, $ident)
    {
        return true;
    }

    public function replace($id, $fileObject )
    {
        if($this->memcache->set($id,file_get_contents($file),0)){
            return $id;
        }else{
            return false;
        }
    }

    private function __getIdent( $fileObject )
    {
        $ident = $this->_ident().'.'.$fileObject->getClientOriginalExtension();
        $result['ident'] = $this->mediaDirectory.$ident;
        return $result;
    }

    private function _ident()
    {
        $id = md5(microtime().base_certificate::get());
        $id = '/'.substr($id,0,2).'/'.substr($id,2,2).'/'.$id;
        return $id;
    }

    function remove($id){
        if($id){
            return $this->memcache->delete($id,10);
        }else{
            return true;
        }
    }

    public function getFile($id)
    {
        if($type=='public'){
            $base_dir = '/public/files';
        }elseif($type=='private'){
            $base_dir = '/data/private';
        }else{
            $base_dir = '/public/images';
        }
        $tmpfile = tempnam(TMP_DIR,'ttprosystem');
        $mkey = $base_dir.$id;
        if($id && file_put_contents($tmpfile,$this->memcache->get($mkey))){
            return $tmpfile;
        }else{
            return true;
        }
    }
}
