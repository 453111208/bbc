<?php
/**
 * ShopEx licence
 *
 * @category ecos
 * @package image.lib
 * @author shopex ecstore dev dev@shopex.cn
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @version 0.1
 */

/**
 * 实现finder页面列表
 * @category ecos
 * @package image.lib.finder
 * @author shopex ecstore dev dev@shopex.cn
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class image_finder_image{

    var $detail_basic = '图片详细信息';
    var $column_img = '图片';
    function __construct($app){
        $this->app = $app;
    }

    /**
     * finder下拉详细展示页面
     * @param string image id
     * @return string 详细页面的html
     */
    function detail_basic($image_id){
        $app = app::get('image');

        $image = $app->model('images');
        $image_info = $image->dump($image_id);
        $allsize = app::get('image')->getConf('image.default.set');

        $pagedata['allsize'] = $allsize;
        $pagedata['image'] = $image_info;

        return view::make('image/finder/image.html', $pagedata)->render();
    }

    /**
     * finder img列的链接修改
     * @param array 某行具体数据的数组
     * @return string 链接html
     */
    function column_img(&$colList, $list){
        foreach($list as $k=>$row)
        {
            $limitwidth = 50;

            $maxsize = max($row['width'],$row['height']);

            if($maxsize>$limitwidth){
                $size ='width=';
                $size.=$row['width']-$row['width']*(($maxsize-50)/$maxsize);
                $size.=' height=';
                $size.=$row['height']-$row['height']*(($maxsize-50)/$maxsize);
            }else{
                $size ='width='.$row['width'].' height='.$row['height'];
            }

            $colList[$k] = '<div  style="width:50px;height:50px;display:block;font-family:Arail;vertical-align: middle;display:table-cell;font-size:42.5px;padding:1px;background:#fff;"><a href="'.$row['url'].'" target="_blank" style="display:block;">
<img src="'.$row['url'].'" '.$size.' /></a></div>';
        }
    }
}
