<?php

use Endroid\QrCode\QrCode;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class image_api_storeQrcode {

    /**
     * 接口作用说明
     */
    public $apiDescription = '生成二维码，并且保存';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        $return['params'] = array(
            'text' => ['type'=>'string','valid'=>'required','description'=>'用于生成二维码的文字,URL','example'=>'http://localhost/','default'=>''],
            'name' => ['type'=>'string','valid'=>'required','description'=>'生成二维码的图片名称','example'=>'我是二维码','default'=>'二维码'],
            'shop_id' => ['type'=>'string','valid'=>'','description'=>'生成二维码的店铺ID','example'=>'1','default'=>''],
            'size' => ['type'=>'string','valid'=>'','description'=>'生成二维码大小','example'=>'300','default'=>'300'],
        );

        return $return;
    }

    public function store($params)
    {
        $text = $params['text'];
        $size = $params['size'] ? $params['size'] : 300;
        $qrCode = new QrCode();
        $qrCodeContent = $qrCode
            ->setText($text)
            ->setSize(300)
            ->setPadding(10)
            ->setErrorCorrection('high')
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
            ->setLabelFontSize(16)
            ->getDataUri('png');

        $tmpTarget = tempnam(TMP_DIR, 'qrCode');
        file_put_contents($tmpTarget, $qrCodeContent);

        $imageParams = getimagesize($tmpTarget);
        $size = filesize($tmpTarget);
        $imageName = $params['name'].'.png';
        $fileObject = new UploadedFile($tmpTarget, $imageName, $imageParams['mime'], $size, 0, true);

        $storager = kernel::single('base_storager');
        $result = $storager->upload($fileObject);
        list($url,$ident,$storage) = explode('|', $result);

        $insertData['url'] = kernel::get_host_mirror_img().$url;
        $insertData['ident'] = $ident;
        $insertData['storage'] = $storage;

        $insertData['width'] = $imageParams['width'];
        $insertData['height'] = $imageParams['height'];
        $insertData['size'] = $size;
        $insertData['image_name'] = $imageName;
        $insertData['img_type'] = 'qrcode';
        $insertData['last_modified'] = time();

        $insertData['target_id'] = '0';
        $insertData['target_type'] = 'admin';
        $insertData['disabled'] = 0;

        $this->objMdlImage = app::get('image')->model('images');
        if( $row = $this->objMdlImage->getRow('id',['url'=>$insertData['url'],'target_id'=>$insertData['target_id'],['target_type'=>$insertData['target_type']]]) )
        {
            $this->objMdlImage->update($insertData, ['id'=>$row['id']]);
        }
        else
        {
            $this->objMdlImage->insert($insertData);
        }
        unlink($file);

        return $insertData['url'];
    }
}
