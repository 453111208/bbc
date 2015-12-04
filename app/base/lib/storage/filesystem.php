<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_storage_filesystem implements base_interface_storager {

    public function __construct()
    {
        $this->mediaDirectory = str_replace(PUBLIC_DIR,'',MEDIA_DIR);
    }

    /**
     * 存储文件
     *
     * @param object $fileObject 继承SplFileInfo封装的类
     */
    public function save( $fileObject )
    {
        $result = $this->__getIdent($fileObject);

        if( !$result ) return false;

        $fileObject->move($result['directory'], $result['name']);
        $data['ident'] = $result['ident'];
        $data['url'] = $this->mediaDirectory.$result['ident'];
        return $data;
    }

    /**
     * 根据原有的图片生成指定大小的图片
     *
     * @param $fileObject
     * @param $ident 存储的唯一值
     */
    public function rebuild($fileObject, $ident)
    {
        $pathInfo = pathinfo($ident);
        $directory = MEDIA_DIR.$pathInfo['dirname'];
        $name = $pathInfo['filename'].'.'.$pathInfo['extension'];
        return $fileObject->move($directory, $name);
    }

    private function __getIdent( $fileObject )
    {
        $file = $fileObject->getPathname();
        $extension .= '.'.$fileObject->getClientOriginalExtension();

        $ident = sha1(uniqid('', true).$file.microtime());
        $directory = '/'.$ident{0}.$ident{1}.'/'.$ident{2}.$ident{3}.'/'.$ident{4}.$ident{5}.'/';
        $fileName = substr($ident,6).substr(md5(($file).microtime()),0,6).$extension;
        $uri = $directory.$fileName;
        //该图片已存在
        if( file_exists(MEDIA_DIR.$uri) ) return false;

        $data['ident'] = $uri;
        $data['directory'] = MEDIA_DIR.$directory;
        $data['name'] = $fileName;
        return $data;
    }

    function remove($key){
        if( $key && file_exists(MEDIA_DIR.$key) )
        {
            return unlink(MEDIA_DIR.$key);
        }
        else
        {
            return true;
        }
    }

    /**
     * 获取文件
     *
     * @param $key 文件存储key
     */
    public function getFile( $ident )
    {
        if( $ident && file_exists(MEDIA_DIR.$ident) )
        {
            return MEDIA_DIR.$ident;
        }

        return false;
    }
}
