<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class site_theme_file
{

    public function __construct() 
    {
        $this->themesdir = array('wap'=>WAP_THEME_DIR, 'pc'=>THEME_DIR);
    }

    //获取单一模板文件路径，这里是图片和xml等
    function get_src($theme, $uriname){
        $theme_url = kernel::get_themes_host_url();
        $preview_prefix = $theme_url.'/'.$theme;
        $src = $preview_prefix.'/'.$uriname;
        return $src;
    }

    //是否有备份文件
    function is_themme_bk($theme, $uriname){
        if(file_exists(THEME_DIR . '/' . $theme . '/'.$uriname)) {
            $is_theme_bk = 'true';
        }else{
            $is_theme_bk = 'false';
        }
        return $is_theme_bk;
    }

    //模板前缀
    function preview_prefix($theme){
        $theme_url = kernel::get_themes_host_url();
        $preview_prefix = $theme_url.'/'.$theme;
        return $preview_prefix;
    }

    //保存备份文件
    function bak_save($theme, $data){
        if(file_put_contents(THEME_DIR . '/' . $theme . '/theme_bak.xml', $data)) {
            $flag = true;
        } else {
            $flag = false;
        }
        return $flag;
    }

    function check($theme,&$msg=''){
        if(empty($theme)){
            $msg = app::get('site')->_('缺少参数');
            return false;
        }
        /** 权限校验 **/
        if($theme && preg_match('/(\..\/){1,}/', $theme)){
            $msg = app::get('site')->_('非法操作');
            return false;
        }
        $dir = THEME_DIR . '/' . $theme;
        if (!is_dir($dir)){
            $msg = app::get('site')->_('路径不存在');
            return false;
        }
        return true;
    }

}
