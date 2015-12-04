<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class image_command_imagedata extends base_shell_prototype{

    public $command_update = '升级图片存储';

    public $command_update_options = array(
        'move'=>array('title'=>'将历史图片库从sdb_image_image移动到sdb_image_images','short'=>'mv'),
        'imageIdToUrl'=>array('title'=>'升级图片存储，将数据库中存储的image_id转为URL','short'=>'url'),
        'uphost'=>array('title'=>'更换图片存储URL地址的域名'),
    );

    //数据库中存储图片的表和对应的字段
    public $params = [
            array('app'=>'sysaftersales','model'=>'aftersales', 'col'=>'evidence_pic'),
            array('app'=>'syscategory','model'=>'brand', 'col'=>'brand_logo'),
            array('app'=>'syscategory','model'=>'prop_values', 'col'=>'prop_image'),
            array('app'=>'sysitem','model'=>'item', 'col'=>['image_default_id','list_image']),

            array('app'=>'sysrate','model'=>'appeal', 'col'=>['evidence_pic']), //appeal_log
            array('app'=>'sysrate','model'=>'traderate', 'col'=>['item_pic','rate_pic']),
            array('app'=>'sysshop','model'=>'shop', 'col'=>['shop_logo','shopuser_identity_img']),
            array('app'=>'sysshop','model'=>'shop_info', 'col'=>['license_img','corporate_identity_img','tissue_code_img','tax_code_img']),


            array('app'=>'sysshop','model'=>'shop_rel_brand', 'col'=>'brand_warranty'),
            array('app'=>'sysstat','model'=>'item_statics', 'col'=>'pic_path'),
            array('app'=>'systrade','model'=>'cart', 'col'=>'image_default_id'),
            array('app'=>'systrade','model'=>'order', 'col'=>'pic_path'),
            array('app'=>'systrade','model'=>'order_complaints', 'col'=>'image_url'),

            array('app'=>'sysuser','model'=>'shop_fav', 'col'=>'shop_logo'),
            array('app'=>'sysuser','model'=>'user_fav', 'col'=>'image_default_id'),
            array('app'=>'sysuser','model'=>'user_grade', 'col'=>'grade_logo'),

            array('app'=>'syspromotion','model'=>'coupon_item', 'col'=>'image_default_id'),
            array('app'=>'syspromotion','model'=>'fulldiscount_item', 'col'=>'image_default_id'),
            array('app'=>'syspromotion','model'=>'fullminus_item', 'col'=>'image_default_id'),
        ];

    public function command_update()
    {
        $args = func_get_args();
        $this->args = $args;

        $options = $this->get_options();
        if($options){
            foreach($options as $key=>$val){
                if($val){
                    logger::info(sprintf('执行 %s ', $this->command_update_options[$key]['title']));
                    $this->$key();
                }
            }
        }else{
            $options = $this->command_update_options;
            foreach($options as $key=>$val){
                if( $key == 'uphost' ) continue;
                logger::info(sprintf('执行 %s ', $this->command_update_options[$key]['title']));
                $this->$key();
            }
        }
    }

    /**
     * 将image表中的历史数据转存到images中
     */
    public function move()
    {
        $objImage = app::get('image')->model('image');
        $objImages = app::get('image')->model('images');
        $db = app::get('systrade')->database();

        $count = $objImage->count();
        logger::info(sprintf('Total %d image', $count));
        logger::info(sprintf('image data from sdb_image_image move to sdb_image_images ... start.'));
        $pagesize = 10;
        for($i=0; $i<$count; $i+=$pagesize)
        {
            $oldImages = $objImage->getList('*', array(), $i, $pagesize);
            if( empty($oldImages) ) continue;

            $imageIds = array_column($oldImages,'image_id');
            $oldImagesAttach = app::get('image')->model('image_attach')->getList('*', array('image_id'=>$imageIds));
            $oldImagesAttach = array_bind_key($oldImagesAttach,'image_id');

            foreach( $oldImages as $row )
            {
                $insert = array();
                if( !$row['ident'] ) continue;
                if( !file_exists(MEDIA_DIR.$row['ident']) ) continue;

                $insert['storage'] = $row['storage'];
                $insert['image_name'] = $row['image_name'];
                $insert['ident'] = $row['ident'];
                $insert['url'] = kernel::get_host_mirror_img().str_replace(PUBLIC_DIR,'',MEDIA_DIR).$row['ident'];
                $insert['width'] = $row['width'];
                $insert['height'] = $row['height'];
                $insert['last_modified'] = $row['last_modified'];

                $insert['target_id'] = $oldImagesAttach[$row['image_id']]['target_id'];
                $insert['target_type'] = $oldImagesAttach[$row['image_id']]['target_type'];

                try
                {
                    $objImages->insert($insert);
                }
                catch (Exception $e)
                {
                    print_r($e);
                }
            }
        }

        logger::info(sprintf('image data from sdb_image_image move to sdb_image_images ... ok.'."\n"));

        return true;
    }

    public function uphost()
    {
        logger::info(sprintf('new host : %s', kernel::get_host_mirror_img()));

        if( $this->args[0] &&  strpos($this->args[0],'://')   )
        {
            $this->oldHost = $this->args[0];
        }
        else
        {
            logger::info(sprintf('请执行命令：image:imagedata updat --uphost http://localhost(%s)', '以前的图片存储地址域名'));
        }

        $objImages = app::get('image')->model('images');
        $count = $objImages->count();
        $pagesize = 100;
        for($i=0; $i<$count; $i+=$pagesize)
        {
            $data = $objImages->getList('*', array(), $i, $pagesize);
            if( $data )
            {
                $this->doUpdata($objImages, $data, 'id', 'url',true);
            }
        }

        foreach( $this->params as $row )
        {
            $model = app::get($row['app'])->model($row['model']);
            $idColumns = $model->idColumn;
            $cols = $row['col'];
            $rows = is_array($cols) ? implode(',',$cols) : $cols;
            $idColumn = is_array($idColumns) ? implode(',',$idColumns) : $idColumns;
            $pagesize = 100;
            $count = $model->count();
            for($i=0; $i<$count; $i+=$pagesize)
            {
                $data = $model->getList($rows.','.$idColumn,array(),$i,$pagesize);
                if( $data )
                {
                    $this->doUpdata($model, $data, $idColumn, $cols,true);
                }
            }
        }

        $this->preWidgetsInstance(true);
        //$this->preSkuSpecDesc(true);
        $this->preEnterapply(true);
        $this->preConfPic(true);
    }

    /**
     * 将旧的图片image_id转为URL存储
     */
    public function imageIdToUrl()
    {
        foreach( $this->params as $row )
        {
            $model = app::get($row['app'])->model($row['model']);
            $cols = $row['col'];
            $idColumns = $model->idColumn;
            $rows = is_array($cols) ? implode(',',$cols) : $cols;
            $idColumn = is_array($idColumns) ? implode(',',$idColumns) : $idColumns;
            $pagesize = 100;
            $count = $model->count();
            for($i=0; $i<$count; $i+=$pagesize)
            {
                $data = $model->getList($rows.','.$idColumn,array(),$i,$pagesize);
                if( $data )
                {
                    $this->doUpdata($model, $data, $idColumn, $cols);
                }
            }

            logger::info(sprintf('update sdb_%s_%s image_id converted to url ... ok',$row['app'],$row['model']));
        }

        $this->preWidgetsInstance();
        //$this->preSkuSpecDesc();
        $this->preEnterapply();
        $this->preConfPic();

        return true;
    }

    /**
     * 执行图片替换操作
     *
     * @param $model 需要替换的model对象
     */
    private function doUpdata($model, $data, $idColumn, $cols, $host=false)
    {
        $upData = array();

        foreach( (array)$data as $key=>$row )
        {
            if( is_array($cols) )
            {
                foreach( $cols as $c )
                {
                    if( $host )
                    {
                        $url = $this->upImageHost($row[$c]);
                    }
                    else
                    {
                        $url = $this->preImageIdToUrl($row[$c]);
                    }
                    if( !$url ) continue;
                    $upData[$c] = $url;
                }
            }
            else
            {
                if( $host )
                {
                    $url = $this->upImageHost($row[$cols]);
                }
                else
                {
                    $url = $this->preImageIdToUrl($row[$cols]);
                }
                if( !$url ) continue;
                $upData[$cols] = $url;
            }

            if( !$upData ) continue;

            if( is_array($idColumn) )
            {
                foreach( $idColumn as $id )
                {
                    $where[$id] = $row[$id];
                }
            }
            else
            {
                $where[$idColumn] = $row[$idColumn];
            }
            $model->update($upData, $where);
        }

        return true;
    }


    private function preEnterapply($uphost=false)
    {
        $model = app::get('sysshop')->model('enterapply');
        $pagesize = 100;
        $count = $model->count();
        for($i=0; $i<$count; $i+=$pagesize)
        {
            $data = $model->getList('enterapply_id,shop_info',array(),$i,$pagesize);
            foreach ($data as $row )
            {
                $shop_info = unserialize($row['shop_info']);
                if( $uphost )
                {
                    $shop_info['corporate_identity_img'] = $this->upImageHost($shop_info['corporate_identity_img']);
                    $shop_info['license_img'] = $this->upImageHost($shop_info['license_img']);
                    $shop_info['tissue_code_img'] = $this->upImageHost($shop_info['tissue_code_img']);
                    $shop_info['tax_code_img'] = $this->upImageHost($shop_info['tax_code_img']);
                    $shop_info['brand_warranty'] = $this->upImageHost($shop_info['brand_warranty']);
                    $shop_info['shopuser_identity_img'] = $this->upImageHost($shop_info['shopuser_identity_img']);
                }
                else
                {
                    $shop_info['corporate_identity_img'] = $this->preImageIdToUrl($shop_info['corporate_identity_img']);
                    $shop_info['license_img'] = $this->preImageIdToUrl($shop_info['license_img']);
                    $shop_info['tissue_code_img'] = $this->preImageIdToUrl($shop_info['tissue_code_img']);
                    $shop_info['tax_code_img'] = $this->preImageIdToUrl($shop_info['tax_code_img']);
                    $shop_info['brand_warranty'] = $this->preImageIdToUrl($shop_info['brand_warranty']);
                    $shop_info['shopuser_identity_img'] = $this->preImageIdToUrl($shop_info['shopuser_identity_img']);
                }

                $shop_info = serialize($shop_info);
                $model->update(['shop_info'=>$shop_info], ['enterapply_id'=>$row['enterapply_id']]);
            }
        }
        echo 'update sysshop_enterapply image_id converted to url ... ok';
    }

    private function preSkuSpecDesc($uphost=false)
    {
        $model = app::get('sysitem')->model('sku');
        $pagesize = 100;
        $count = $model->count();
        for($i=0; $i<$count; $i+=$pagesize)
        {
            $data = $model->getList('spec_desc,sku_id',array(),$i,$pagesize);
        }

        logger::info(sprintf('update sdb_%s_%s image_id converted to url ... ok','sysdecorate_widgets_instance'));

    }

    /*表: sysdecorate_widgets_instance:

        1：widgets_type是wapimageslider的  字段params里面的sliderImage的值

        2：widgets_type是wapslider的  字段params里面的sliderImage的值
     */
    private function preWidgetsInstance($uphost=false)
    {
        $widgetsMdl = app::get('sysdecorate')->model('widgets_instance');
        $data = $widgetsMdl->getList('params,widgets_id',['widgets_type'=>array('shopsign')],0,10000);
        foreach( $data as $row)
        {
            if( !$row['params']['image_id'] ) continue;
            if( $uphost )
            {
                $url = $this->upImageHost($row['params']['image_id']);
            }
            else
            {
                $url = $this->preImageIdToUrl($row['params']['image_id']);
            }
            if( !$url ) continue;

            $params['image_id'] = $url;
            $widgetsMdl->update(['params'=>$params], ['widgets_id'=>$row['widgets_id']]);
        }

        logger::info(sprintf('update sdb_%s_%s image_id converted to url ... ok','sysdecorate_widgets_instance'));
    }

    private function preConfPic($uphost=false)
    {
        $logoId = app::get('sysconf')->getConf('sysconf_setting.wap_logo');
        $wapmacLogo = app::get('sysconf')->getConf('sysconf_setting.wapmac_logo');
        $propDefaultPic = app::get('syscategory')->getConf('prop.default.pic');

        $imageSetParams = app::get('image')->getConf('image.set');
        $imageDefault = app::get('image')->getConf('image.default.set');
        foreach( $imageDefault as $k=>$row )
        {
            if( $uphost )
            {
                $imageDefault[$k]['default_image'] = $this->upImageHost($row['default_image']);
                $imageSetParams[$k]['default_image'] = $this->upImageHost($imageSetParams[$k]['default_image']);
            }
            else
            {
                $imageDefault[$k]['default_image'] = $this->preImageIdToUrl($row['default_image']);
                $imageSetParams[$k]['default_image'] = $this->preImageIdToUrl($imageSetParams[$k]['default_image']);
            }
        }
        app::get('image')->setConf('image.set', $imageSetParams);
        app::get('image')->setConf('image.default.set',$imageDefault);

        if( $uphost )
        {
            $wapLogo = $this->upImageHost($logoId);
            $propDefaultPic = $this->upImageHost($propDefaultPic);
            $wapmacLogo = $this->upImageHost($wapmacLogo);
        }
        else
        {
            $wapLogo = $this->preImageIdToUrl($logoId);
            $propDefaultPic = $this->preImageIdToUrl($propDefaultPic);
            $wapmacLogo = $this->preImageIdToUrl($wapmacLogo);
        }

        if( $wapLogo )
        {
            app::get('sysconf')->setConf('sysconf_setting.wap_logo', $wapLogo);
        }
        if( $propDefaultPic )
        {
            app::get('syscategory')->setConf('prop.default.pic', $propDefaultPic);
        }
        if( $wapmacLogo )
        {
            app::get('sysconf')->setConf('sysconf_setting.wapmac_logo', $wapmacLogo);
        }
    }

    /**
     * 替换图片地址域名
     *
     * @param $imageUrl 图片URL地址
     */
    private function upImageHost($imageUrl)
    {
        if( empty($imageUrl) ) return true;

        if( $this->oldHost )
        {
            $newUrl = str_replace($this->oldHost, kernel::get_host_mirror_img(), $imageUrl);
            logger::info(sprintf('update %s converted to %s ... ok', $imageUrl, $newUrl));
        }

        return $newUrl;
    }

    /**
     * 升级历史数据，将image_id转为URL存储
     */
    private function preImageIdToUrl($imageId)
    {
        if( empty($imageId) ) return false;

        if($imageId && strpos($imageId,'://'))
        {
            return $imageId;
        }

        $imageIds = explode(',',$imageId);

        $imageMdl = app::get('image')->model('image');

        $data = $imageMdl->getList('*',['image_id'=>$imageIds]);

        foreach((array)$data as $row )
        {
            if( $row['url'] && strpos($row['url'],'://') )
            {
                $url[] = $row['url'];
            }
            else
            {
                $resource_host_url = kernel::get_host_mirror_img();
                $url[] = $resource_host_url.str_replace(PUBLIC_DIR,'',MEDIA_DIR).'/'.$row['ident'];
            }
        }

        return implode(',',$url);
    }
}

