<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class toputil_ctl_image {

    public function uploadImages()
    {
        $objLibImage = kernel::single('image_data_image');

        $file = $this->__getFile(input::file());

        $imageType = input::get('type',false);
        $imageFrom = input::get('from',false);

        foreach( (array)$file as $key=>$fileObject  )
        {
            try
            {
                $imageData = $objLibImage->store($fileObject,$imageFrom,$imageType);

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
                $objLibImage->rebuild($imageData['ident'], $sizes);
            }
            catch(Exception $e)
            {
                $msg = $e->getMessage();
                $result = array('error'=>true, 'message'=>$msg);
                return response::json($result);
            }
            $imageSrc[$key]['url'] = $imageData['url'];
            $imageSrc[$key]['image_id'] = $imageData['url'];
        }

        $result = array('success'=>true, 'data'=>$imageSrc);
        return json_encode($result);
    }

    private function __getFile($file)
    {
        $objFile = current($file);
        if( !is_object($objFile) )
        {
            $file = $this->__getFile($objFile);
        }
        return $file;
    }

    /**
     * 根据itemId获取图片
     */
    public function getItemPic()
    {
        $itemId = input::get('itemIds');
        $picData = kernel::single('sysitem_item_info')->getItemDefaultPic($itemId);
        if( $picData[$itemId]['image_default_id'] )
        {
            $result['url'] = $picData[$itemId]['image_default_id'];
        }
        return response::json($result);
    }

}
