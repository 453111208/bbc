 <?php
/**
 * ShopEx licence
 *
 * @category ecos
 * @package image.controller
 * @author shopex ecstore dev dev@shopex.cn
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @version 0.1
 */

/**
 * 后台图片管理类
 * @category ecos
 * @package image.controller.admin
 * @author shopex ecstore dev dev@shopex.cn
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

use Symfony\Component\HttpFoundation\File\UploadedFile;

class image_ctl_admin_manage extends desktop_controller
{
    /**
     * @var 定义控制器属于哪个菜单区域
     */
    var $workground = 'image_ctl_admin_manage';

    /**
     * act==index页面入口
     * @param null
     * @return string html内容
     */
    function index(){
        $action = array(
                array('label'=>app::get('image')->_('上传新图片'),'href'=>'?app=image&ctl=admin_manage&act=image_swf_uploader'
                            ,'target'=>'dialog::{title:\''.app::get('image')->_('上传图片').'\',width:500,height:350}'),
                array('label'=>app::get('image')->_('添加网络图片'),'href'=>'?app=image&ctl=admin_manage&act=image_www_uploader'
                            ,'target'=>'dialog::{title:\''.app::get('image')->_('添加网络图片').'\',width:550,height:200}'),
                array( 'label' => app::get('image')->_('删除'),
                        'submit' => '?app=image&ctl=admin_manage&act=doDelete',
                        'confirm' => app::get('image')->_('确定删除？'),
                ),
            );
        return $this->finder('image_mdl_images',array(
            'title'=>app::get('image')->_('图片管理'),
            'actions'=>$action,
            //'use_buildin_set_tag'=>true,
            'use_buildin_filter'=>true,
            //'use_buildin_tagedit'=>true
            'base_filter'=>array('disabled'=>0),
            'use_buildin_delete'=>false,
        ));
    }

    public function doDelete()
    {
        $this->begin("?app=image&ctl=admin_manage&act=index");
        $id = input::get('id');
        if( $id )
        {
            app::get('image')->model('images')->update(['disabled'=>1], ['id'=>$id]);
        }

        if( input::get('isSelectedAll') == '_ALL_' )
        {
            $msg = '不能删除全部图片';
            return $this->end(false,$msg);
        }

        $msg = '删除成功';
        $this->adminlog("删除图片[id:{$id}]", 1);
        return $this->end(true,$msg);
    }

    /**
     * 显示上传swf的入口
     * @param null
     * @return string html
     */
    function image_swf_uploader(){
        $mdl_img = $this->app->model('image');
        $pagedata['currentcount'] = $mdl_img->count();
        $pagedata['ssid'] =  kernel::single('base_session')->sess_id();
        $pagedata['IMAGE_MAX_SIZE'] = IMAGE_MAX_SIZE;
        return view::make('image/image_swf_uploader.html', $pagedata);
    }

    /**
     * 图片上传的接口
     * @param null
     * @return string 上传的消息
     */
    public function image_upload()
    {

       $mdl_img   = $this->app->model('image');

       $objLibImage = kernel::single('image_data_image');
       $imageData = $objLibImage->store($_FILES['upload_item'],null,'admin');
       if(!$imageData['url'])
       {
            header('Content-Type:text/html; charset=utf-8');
            echo "{error:'".app::get('image')->_('图片上传失败')."',splash:'true'}";
            exit;
       }

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

       $image_id = $imageData['url'];
       $image_src = $imageData['url'];

       $this->_set_tag($imageData);
       if($callback = $_REQUEST['callbackfunc'])
       {
           $_return = "<script>try{parent.$callback('$image_id','$image_src')}catch(e){}</script>";
       }

       $_return.="<script>parent.MessageBox.success('".app::get('image')->_('图片上传成功')."');</script>";
       $this->adminlog("上传图片[image_src:{$image_src}]", 1);
       echo $_return;
    }

    /**
     * 设置图片的tag-本类私有方法
     * @param null
     * @return null
     */
    public function _set_tag($imageData)
    {
       $tagctl   = app::get('desktop')->model('tag');
       $tag_rel   = app::get('desktop')->model('tag_rel');
       $data['rel_id'] = $image_id;
       $tags = explode(' ',$_POST['tag']['name']);
       $data['tag_type'] = 'image';
       $data['app_id'] = 'image';
       foreach($tags as $key=>$tag)
       {
           if(!$tag) continue;
            $data['tag_name'] = $tag;
            $tagctl->save($data);
            if($data['tag_id'])
            {
                $data2['tag']['tag_id'] = $data['tag_id'];
                $data2['rel_id'] = $imageData['id'];
                $data2['tag_type'] = 'image';
                $data2['app_id'] = 'image';
                $tag_rel->save($data2);
                unset($data['tag_id']);
            }
       }
    }

    /**
     * 上传网络图片地址-本类私有方法
     * @param null
     * @return string html内容
     */
    function image_www_uploader()
    {
        if($_POST['upload_item'])
        {
            $objLibImage = kernel::single('image_data_image');
            $image = $objLibImage->store($_POST['upload_item'],null,'admin');
            $objLibImage->rebuild($image['ident']);
            $image_src = base_storager::modifier($image['url']);
            $image_id = $image['url'];
            $this->_set_tag($image);
            if($callback = $_REQUEST['callbackfunc']){

                 $_return = "<script>try{parent.$callback('$image_id','$image_src')}catch(e){}</script>";

            }

            $_return.="<script>parent.MessageBox.success('".app::get('image')->_('图片上传成功')."');</script>";

            echo $_return;
            echo <<<EOF
<div id="upload_remote_image"></div>
<script>
try{
    if($('upload_remote_image').getParent('.dialog'))
    $('upload_remote_image').getParent('.dialog').retrieve('instance').close();
}catch(e){}
</script>
EOF;
        }else{
            $html  ='<div class="division"><h5>'.app::get('image')->_('网络图片地址：').'</h5>';
            $html .= view::ui()->form_start(array('method'=>'post'));
            $html .= view::ui()->input(array(

                'type'=>'url',
                'name'=>'upload_item',
                'value'=>'http://',

                'style'=>'width:70%'
                ));
            $html .='</div>';
            $html .= view::ui()->form_end();
            echo $html."";

        }
    }

    /**
     * 远程swf的页面显示
     * @param null
     * @return string html内容
     */
    function image_swf_remote(){
        $objLibImage = kernel::single('image_data_image');
        $imageData = $objLibImage->store($_FILES['Filedata'],null,'admin');
        $objLibImage->rebuild($imageData['ident']);
        $pagedata['image_id'] = $imageData['url'];
        return view::make('image/image_swf_uploader_reponse.html', $pagedata);

    }

    /**
     * 动态的swf页面显示
     * @param null
     * @return string html内容
     */
    function gimage_swf_remote(){

        $objLibImage = kernel::single('image_data_image');
        $imageData = $objLibImage->store($_FILES['Filedata'],null,'admin');

        $objLibImage->rebuild($imageData['ident']);

        $pagedata['gimage']['image_id'] = $imageData['url'];

        header('Content-Type:text/html; charset=utf-8');
        return view::make('image/gimage.html', $pagedata);
    }

    /**
     * 图片浏览器
     * @param int 第几页的页面
     * @return string html内容
     */
    function image_broswer($page=1){

        $pagelimit = 10;

        $otag = app::get('desktop')->model('tag');
        $oimage = $this->app->model('images');
        $tags = $otag->getList('*',array('tag_type'=>'image'));

        $pagedata['type'] = $_GET['type'];
        $pagedata['tags'] = $tags;
        return view::make('image/image_broswer.html', $pagedata);

    }

    /**
     * 图片管理列表内容显示
     * @param string 图片的tag
     * @param int 第几页的页面
     * @return string html内容
     */
    function image_lib($tag='',$page=1){
        $pagelimit = 12;

        //$otag = $this->app->model('tag');
        $oimage = $this->app->model('images');

        //$tags = $otag->getList('*',array('tag_type'=>'image'));
        $filter = array();
        if($tag){
            $filter = array('tag'=>array($tag));
        }
        $images = $oimage->getList('*',$filter,$pagelimit*($page-1),$pagelimit);
        $count = $oimage->count($filter);

        $limitwidth = 100;

        foreach($images as $key=>$row){
            $maxsize = max($row['width'],$row['height']);
            if($maxsize>$limitwidth){
                $size ='width=';
                $size.=$row['width']-$row['width']*(($maxsize-$limitwidth)/$maxsize);
                $size.=' height=';
                $size.=$row['height']-$row['height']*(($maxsize-$limitwidth)/$maxsize);
            }else{
                $size ='width='.$row['width'].' height='.$row['height'];
            }
            $row['size'] = $size;
            $images[$key] = $row;
        }

        $pagedata['images'] = $images;
        $pagedata['pagers'] = view::ui()->pager(array(
            'current'=>$page,
            'total'=>ceil($count/$pagelimit),
            'link'=>'?app=image&ctl=admin_manage&act=image_lib&p[0]='.$tag.'&p[1]=%d',
            ));
        return view::make('image/image_lib.html', $pagedata);

     }

    /**
     * 删除图片
     * @param nulll
     * @return string 图片删除信息json
     */
    function image_del()
    {
        $image_id = $_GET['image_id'];
        $oimage = $this->app->model('image');
        if($oimage->delete(array('image_id'=>$image_id)))
        {
            header('Content-Type:application/json; charset=utf-8');
            echo '{success:"'.app::get('image')->_('删除成功').'"}';
        }
   }

    /**
     * 图片大小配置
     * @param nulll
     * @return string 显示配置页面内容
     */
    public function imageset()
    {
        header("cache-control: no-store, no-cache, must-revalidate");
        $image = app::get('image')->model('image');
        $objLibImage = kernel::single('image_data_image');

       $allsize = array();
        if( input::get('pic') )
        {
            $imageSet = input::get('pic');
            $curImageSet = $this->app->getConf('image.set');

            foreach($imageSet as $size=>$item)
            {
                if($item['wm_type'] == 'text')
                {
                    $image_id = '';
                    if($curImageSet && $curImageSet[$size] && $curImageSet[$size]['wm_text_image'])
                    {
                        $image_id = $curImageSet[$size]['wm_text_image'];
                    }

                    if(!function_exists('imagettftext'))
                    {
                        trigger_error('gd函数库的版本过低,请配置高于2.0.28的版本',E_USER_NOTICE);
                        echo "Notice:gd函数库的版本过低,请配置高于2.0.28的版本";
                        exit;
                    }
                    #生成文字图片
					$tmpfile = TMP_DIR."/img".time().".png";
					$fontfile = PUBLIC_DIR."/app/".app::get('image')->app_id."/statics/msyh.ttf";
					$img = imagecreatetruecolor(120, 100);
					$color=imagecolorallocate($img,255,255,255);
					imagecolortransparent($img,$color);
					imagefill($img,0,0,$color);
					$textcolor=imagecolorallocate($img,0,0,0);
					imagettftext($img, 16, 0, 0, 50, $textcolor, $fontfile, $item['wm_text']);
					imagesavealpha($img , true);
					imagepng($img,$tmpfile);
					imagedestroy($img);

                    $imageName = substr(strrchr($tmpfile,'/'),1);
                    $size = filesize($tmpfile);
                    $fileObject = new UploadedFile($tmpfile, $imageName, 'image/png', $size, 0, true);
                    $image_id = $objLibImage->store($fileObject,null,'admin');
                    $image_set[$size]['wm_text_image'] = $image_id;
                }
            }
            $preimageset_log = $this->app->getConf('image.set');
            $this->app->setConf('image.set',$imageSet);
            $curImageSet = $this->app->getConf('image.set');
        }

        $def_image_set = $this->app->getConf('image.default.set');
		$minsize_set = false;
        foreach($def_image_set as $k=>$v)
        {
            if(!$minsize_set||$v['height']<$minsize_set['height'])
            {
				$minsize_set = $v;
			}
		}

        $pagedata['allsize'] = $def_image_set;
		$pagedata['minsize'] = $minsize_set;
        $curImageSet = $this->app->getConf('image.set');
        $pagedata['image_set'] = $curImageSet;
        $pagedata['this_url'] = $this->url;
        return $this->page('image/imageset.html', $pagedata);
    }

    /**
     * 查看图片
     * @param nulll
     * @return string html页面内容
     */
    function view_gimage($image_id){
        $pagedata['image_id'] = $image_id;
        return $this->page('image/images.html', $pagedata);
    }

    /**
     * 配置好图片的预览
     * @param nulll
     * @return string html预览页面
     */
    function img_preview(){
        $size = $_GET['size']?$_GET['size']:'L';
        $setting = $_POST['pic'][$size];
        $w = $setting['width'];
        $h = $setting['height'];
        $storager = new base_storager();
        $mdl_img = $this->app->model('image');
        $img_row = $mdl_img->dump($setting['default_image']);

        $tmp_image_id = $mdl_img->gen_id();

        if($setting['wm_type']=='text'&&$setting['wm_text'])
        {
            if(!function_exists('imagettftext')){
				trigger_error('gd函数库的版本过低,请配置高于2.0.28的版本',E_USER_NOTICE);
				echo "Notice:gd函数库的版本过低,请配置高于2.0.28的版本";
				exit;
			}
			#生成文字图片
			//$tmp_water_file = TMP_DIR."/img".time().".png";
			$tmp_water_file = tempnam(TMP_DIR,'img').time().".png";
			$fontfile = PUBLIC_DIR."/app/".app::get('image')->app_id."/statics/msyh.ttf";
			$img = imagecreatetruecolor(120, 100);
			$color=imagecolorallocate($img,255,255,255);
			imagecolortransparent($img,$color);
			imagefill($img,0,0,$color);
			$textcolor=imagecolorallocate($img,0,0,0);
			imagettftext($img, 16, 0, 0, 50, $textcolor, $fontfile, $setting['wm_text']);
			imagesavealpha($img , true);
			imagepng($img,$tmp_water_file);
			imagedestroy($img);

            $setting['wm_text_preview'] = true;
			$setting['wm_text_image'] = $tmp_water_file;
        }

        $objLibImage = kernel::single('image_data_image');
        $tmp_file = $objLibImage->fetch($img_row['image_id']);

        $tmp_target = tempnam(TMP_DIR,'img');
        image_clip::image_resize($tmp_file,$tmp_target,$w,$h);
        if( $setting['wm_type']!='none' && ($setting['wm_text'] || $setting['wm_image']) )
        {
            image_clip::image_watermark($objLibImage,$tmp_target,$setting);
        }
        unlink($tmp_water_file);
        $type = (getimagesize($tmp_target));
        header("Content-Type: {$type[mime]}");
        readfile($tmp_target);
    }
}//End Class
