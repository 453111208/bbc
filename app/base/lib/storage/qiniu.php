<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;

class base_storage_qiniu implements base_interface_storager {

    public function __construct()
    {
        $qiniuConfig = config::get('storager.qiniu');

        $accessKey = $qiniuConfig['auth']['accessKey'];
        $secretKey = $qiniuConfig['auth']['secretKey'];
        $this->auth = new Auth($accessKey, $secretKey);

        $this->bucket = $qiniuConfig['bucket'];

        $this->url = $qiniuConfig['url'];
    }

    public function save( $fileObject )
    {
        $token = $this->auth->uploadToken($this->bucket);
        $uploadMgr = new UploadManager();

        list($ret, $err) = $uploadMgr->putFile($token, null, $fileObject->getPathname());

        if( !$err )
        {
            $data['ident'] = $ret['key'];
            $data['url'] = '/'.$ret['key'];
            return $data;
        }
        else
        {
            throw new Exception($err);
        }
    }

    public function getSizeImageUrl($imageUrl, $size)
    {
        $pathInfo = pathinfo($imageUrl);
        if( !$pathInfo['extension'] )
        {
            $newUrl = $imageUrl.'_'.strtolower($size).'.jpg';
            return $newUrl;
        }

        return false;
    }

    /**
     * 根据原有的图片生成指定大小的图片
     *
     * @param $fileObject
     * @param $ident 存储的唯一值
     */
    public function rebuild($fileObject, $ident)
    {
        return true;
    }

    public function replace($id, $fileObject )
    {
        return false;
    }

    function remove($id)
    {
        return true;
    }

    public function getFile($id)
    {
    }

}
