<?php

/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class base_storager {
    /*
     * 获取默认驱动
     * @access static public
     * @return 驱动类
     */
    static public function get_default_driver()
    {
        return config::get('storager.default', 'filesystem');
    }

    public function __construct()
    {
        $driver = static::get_default_driver();
        $this->class_name = 'base_storage_'.$driver;
        $this->worker = new $this->class_name;
    }

    /**
     * 存储文件
     *
     * @param object $fileObject 继承SplFileInfo封装的类
     */
    public function upload( $fileObject )
    {
        $data = $this->worker->save( $fileObject );
        if( $data )
        {
            $ident_data = $data['url'].'|'.$data['ident'].'|'.substr($this->class_name,strrpos($this->class_name,"_")+1);
            return $ident_data;
        }
        else
        {
            return false;
        }
    }

    /**
     * 根据原有的图片生成指定大小的图片
     *
     * @param $fileObject
     * @param $sizeType 生成图片大小的标志 M
     * @param $ident 原图存储的唯一值 /11/f4/79/97dff16cba63e86d5624cbbd29f2230a17d22fd0.jpg
     */
    public function rebuild($fileObject, $sizeType, $ident)
    {
        if( !$ident || !$sizeType ) return true;

        $newIdent = $this->__newIdent($ident, $sizeType);

        return $this->worker->rebuild($fileObject, $newIdent);
    }

    public function remove($ident)
    {
        return $this->worker->remove($ident);
    }

    /**
     * 获取文件
     *
     * @param $key 文件存储key
     */
    public function getFile($ident)
    {
        return $this->worker->getFile($ident);
    }

    static function modifier($imageUrl,$size='')
    {
        if( $size && $imageUrl)
        {
            $className = 'base_storage_'.config::get('storager.default', 'filesystem');
            $worker = new $className;

            if( method_exists($worker, 'getSizeImageUrl') )
            {
                $newImageUrl = $worker->getSizeImageUrl($imageUrl, $size);
            }

            if( !$newImageUrl )
            {
                $newImageUrl = self::__newIdent($imageUrl, $size);
            }

            return $newImageUrl;
        }

        return $imageUrl;
    }

    private function __newIdent($ident, $sizeType)
    {
        $pathInfo = pathinfo($ident);
        $newIdent = $ident.'_'.strtolower($sizeType).'.'.$pathInfo['extension'];
        return $newIdent;
    }
}
