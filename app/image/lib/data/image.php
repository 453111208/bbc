<?php

use Symfony\Component\HttpFoundation\File\UploadedFile;

class image_data_image {

    public function __construct()
    {
        $this->objMdlImage = app::get('image')->model('images');
    }

    //上传图片支持的后缀
    private $__extension = ['png', 'jpg', 'bmp', 'gif', 'jpeg'];

    private function __getImageParams( $file )
    {
        $imageParams = getimagesize($file);
        $data['width'] = $imageParams[0];
        $data['height'] = $imageParams[1];
        return $data;
    }

    private function __checkImage( $fileObject, $type='max' )
    {
        $extension = $fileObject->getClientOriginalExtension();
        if( !in_array(strtolower($extension), $this->__extension) )
        {
            throw new \LogicException(app::get('image')->_('不支持该图片格式'));
        }

        if( $type == 'max' )
        {
            $maxFilesize = $fileObject->getMaxFilesize();
        }
        $fileSize = $fileObject->getClientSize();
        if( $maxFilesize < $fileSize )
        {
            throw new \LogicException(app::get('image')->_('超出上传图片文件过大'));
        }

        $imageName = $fileObject->getClientOriginalName();
        if( strlen($imageName) > 200 )
        {
            throw new \LogicException(app::get('image')->_('图片文件名称过长，名称不能超过200个字符'));
        }

        return true;
    }

    private function __preFileObject( $fileObject )
    {
        if(substr($fileObject,0,4) == 'http' )
        {
            $fileObject = $this->__getNetworkImage($fileObject);
        }
        elseif( !is_object($fileObject) )
        {
            if( file_exists($fileObject) )
            {
                $file = tempnam(TMP_DIR,'tmpImage');
                kernel::single('base_filesystem')->copy($fileObject, $file);
                $imageParams = getimagesize($file);
                $size = filesize($file);

                $imageName = substr(strrchr($fileObject,'/'),1);
                $fileObject = new UploadedFile($file, $imageName, $imageParams['mime'], $size, 0, true);
            }
            else
            {
                $fileObject = new UploadedFile($fileObject['tmp_name'], $fileObject['name'], $fileObject['type'], $fileObject['size'], $fileObject['error']);
            }
        }

        return $fileObject;
    }

    /**
     * 存储图片接口
     *
     * @param object $fileObject 继承SplFileInfo封装的类
     * @param string $from  上传图片用户类型
     */
    public function store( $fileObject, $from, $imageType, $test=false)
    {
        $fileObject = $this->__preFileObject( $fileObject );

        $this->__checkImage($fileObject);

        $file = $fileObject->getRealPath();
        $imageParams = $this->__getImageParams($file);
        $params['width'] = $imageParams['width'];
        $params['height'] = $imageParams['height'];
        $params['size'] = $fileObject->getClientSize();

        $params['image_name'] = $fileObject->getClientOriginalName();

        $params['img_type'] = $imageType;
        $params['last_modified'] = time();

        $storager = kernel::single('base_storager');
        $result = $storager->upload($fileObject);
        list($url,$ident,$storage) = explode('|', $result);

        $params['url'] = kernel::get_host_mirror_img().$url;
        $params['ident'] = $ident;
        $params['storage'] = $storage;

        $accountData = $this->__imageAttach($from, $test);
        $params['target_id'] = $accountData['target_id'];
        $params['target_type'] = $accountData['target_type'];
        $params['disabled'] = 0;

        if( $row = $this->objMdlImage->getRow('id',['url'=>$params['url'],'target_id'=>$params['target_id'],['target_type'=>$params['target_type']]]) )
        {
            $this->objMdlImage->update($params, ['id'=>$row['id']]);
        }
        else
        {
            $this->objMdlImage->insert($params);
        }
        unlink($file);

        return $params;
    }

    /**
     * 图片ID，关联上用户类型ID
     *
     * @param string $from 上传图片用户类型
     */
    private function __imageAttach($from=false, $test=false)
    {
        if( $from == 'shop' )
        {
            pamAccount::setAuthType('sysshop');
            $data['target_id'] = pamAccount::getAccountId();

            $shopId = app::get('image')->rpcCall('shop.get.loginId',array('seller_id'=>$data['target_id']),'seller');

            if($shopId )
            {
                $data['target_id'] = $shopId;
                $data['target_type'] = 'shop';
            }
            else
            {
                $data['target_type'] = 'seller';
            }
        }
        elseif( $from == 'user' )
        {
            pamAccount::setAuthType('sysuser');
            $data['target_id'] = pamAccount::getAccountId();
            $data['target_type'] = 'user';
        }
        else
        {
            pamAccount::setAuthType('desktop');
            $data['target_id'] = pamAccount::getAccountId();
            $data['target_type'] = 'admin';
        }

        if( !$data['target_id'] && !$test )
        {
            throw new \LogicException(app::get('image')->_('无上传图片权限'));
        }

        return $data;
    }

    /**
     * 商品图片相册图片生成
     *
     * @param $ident 需要生成相册图片唯一值
     * @param $sizes   生成图片大小
     *
     * @return bool
     */
    public function rebuild($ident, $sizes )
    {
        if( !$sizes )
        {
            $imageSetParams = app::get('image')->getConf('image.set');
            $allsize = app::get('image')->getConf('image.default.set');
            foreach($allsize as $s=>$value)
            {
                if( !isset($allsize[$s]) ) break;

                $w = $imageSetParams[$s]['width'];
                $h = $imageSetParams[$s]['height'];
                $wh = $allsize[$s]['height'];
                $wd = $allsize[$s]['width'];
                $sizes[$s]['width'] = $w?$w:$wd;
                $sizes[$s]['height'] = $h?$h:$wh;
            }
        }

        $storager = kernel::single('base_storager');
        $orgFile = $storager->getFile($ident);

        if( !file_exists($orgFile) || !$sizes ) return true;

        foreach($sizes as $s=>$value)
        {
            $tmpTarget = tempnam(TMP_DIR,'img');
            $w = $value['width'];
            $h = $value['height'];

            $orgFileSize = getimagesize($orgFile);
            if( $orgFileSize['0'] < $w )
            {
                $w = $orgFileSize['0'];
            }
            if( $orgFileSize['1'] < $h )
            {
                $h = $orgFileSize['1'];
            }

            image_clip::image_resize($orgFile, $tmpTarget, $w, $h);

            $imageParams = getimagesize($tmpTarget);
            $size = filesize($tmpTarget);
            $fileObject = new UploadedFile($tmpTarget, $images['image_name'], $imageParams['mime'], $size, 0, true);

            $storager->rebuild($fileObject, strtolower($s), $ident);
            unlink($tmpTarget);
        }

        return true;
    }

    /**
     * 存储网络图片
     *
     * @param string $imageUrl 图片URL地址
     */
    public function storeNetworkImage( $imageUrl, $from, $imageType, $test=false )
    {
        $fileObject = $this->__getNetworkImage($imageUrl);
        $imageId = $this->store($fileObject, $from, $imageType, $test);
        $file = $fileObject->getRealPath();
        unlink($file);
        return $imageId;
    }

    private function __getNetworkImage($imageUrl)
    {
        $imageContent = kernel::single('base_httpclient')->get($imageUrl);
        $tmpTarget = tempnam(TMP_DIR, 'imageurl');
        file_put_contents($tmpTarget, $imageContent);

        $imageParams = getimagesize($tmpTarget);
        $size = filesize($tmpTarget);

        $imageName = substr(strrchr($imageUrl,'/'),1);

        if( $num = strpos($imageName,'?') )
        {
            $imageName = substr($imageName,0,$num);
        }

        $fileObject = new UploadedFile($tmpTarget, $imageName, $imageParams['mime'], $size, 0, true);

        return $fileObject;
    }
}
