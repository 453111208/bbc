<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class site_ctl_admin_theme_manage extends site_admin_controller
{

    /*
     * workground
     * @var string
     */
    var $workground = 'site.wrokground.theme';

    //列表
    public function index()
    {
        //默认读取一下themes文件夹，获取文件夹内已有模板@lujy
        kernel::single('site_theme_install')->check_install($this->platform);
        $default_theme = kernel::single('site_theme_base')->get_default($this->platform);
        $themeInfo = app::get('site')->model('themes')->getList('*', array('theme'=>$default_theme));
        $pagedata['site_url'] = url::route('topc');
        $preview = 'preview.jpg';

        if ($themeInfo){
            $pagedata['current_theme'] = $themeInfo[0];
            /** 获取当前模版的信息 **/
            $pagedata['current']['is_themme_bk'] = kernel::single('site_theme_file')->is_themme_bk($themeInfo[0]['theme'], 'theme_bak.xml');
            $preview_prefix = kernel::single('site_theme_file')->preview_prefix($themeInfo[0]['theme']);
            $pagedata['preview_prefix'] = $preview_prefix;
            $src = kernel::single('site_theme_file')->get_src($themeInfo[0]['theme'], $preview);
            $pagedata['current_theme_preview_img'] = $src;

            //设置编辑默认页面
            $defaultIndexFile = kernel::single('site_theme_tmpl')->get_default('index', $default_theme);
            $nodefaultindex = $this->app->model('themes_tmpl')->getList('tmpl_path',array('theme'=>$default_theme,'tmpl_type'=>'index'));
            $pagedata['current']['default_index_file'] = $defaultIndexFile ? $defaultIndexFile : $nodefaultindex[0]['tmpl_path'];
        }
        /** 获取所有已安装的模版 **/
        $all_themes = app::get('site')->model('themes')->getList('*', array('is_used'=>0, 'platform'=>$this->platform));

        foreach ($all_themes as $k=>$arr_theme){
            $all_themes[$k]['is_themme_bk'] = kernel::single('site_theme_file')->is_themme_bk($arr_theme['theme'], 'theme_bak.xml');
            $preview_prefix = kernel::single('site_theme_file')->preview_prefix($arr_theme['theme']);
            $src = kernel::single('site_theme_file')->get_src($arr_theme['theme'], $preview);
            $all_themes[$k]['preview'] = $src;
            $all_themes[$k]['preview_prefix'] = $preview_prefix;
        }
        $pagedata['all_themes'] = $all_themes;
        $pagedata['platform'] = $this->platform;

        return $this->page('site/admin/theme/manage/index.html', $pagedata);

    }//End Function

    function note(){
        $theme = input::get('theme');
        if(!$this->check($theme,$msg))    $this->_error($msg);

        $pagedata['theme'] = $theme;
        $pagedata['platform'] = $this->platform;

        return view::make('site/admin/theme/manage/note.html', $pagedata);
    }//End Function

    function save_note(){
        $this->begin('?app=site&ctl=admin_theme_manage&act=index&platform='.$this->platform);

        $theme = input::get('theme');
        if(!$this->check($theme,$msg))   $this->_error($msg);

        $filter = array(
            'theme'=>$theme
        );
        if (!app::get('site')->model('themes')->update( array('info'=>input::get('info') ), $filter)){
            $this->adminlog("设置模板备注[{$theme}]", 1);
            $this->end(false,app::get('site')->_('备注设置失败！'));
        }else{
            $this->adminlog("设置模板备注[{$theme}]", 0);
            $this->end(true,app::get('site')->_('备注设置成功！'));
        }
    }//End Function

    function detail(){
        $params = input::all();
        if (!$params['id']){
            header("Content-type: text/html; charset=utf-8");
            echo '{error:"'.app::get('site')->_('没有指定具体的模板！').',redirect:null"}';exit;
        }
        $data = app::get('site')->model('themes')->getList('*', array('theme'=>$params['id']));

        $theme = $data[0]['theme'];
        $pagedata['list'] = kernel::single('site_theme_tmpl')->get_edit_list($theme);
        $pagedata['types'] = kernel::single('site_theme_tmpl')->get_name($this->platform);
        $pagedata['theme'] = $theme;
        $pagedata['pagehead_active'] = 'pagem';

        //设置可视化编辑页面（默认and非默认）
        $defaultIndexFile = kernel::single('site_theme_tmpl')->get_default('index',$theme);
        $nodefaultindex = $this->app->model('themes_tmpl')->getList('tmpl_path',array('theme'=>$theme,'tmpl_type'=>'index'));
        $pagedata['current']['default_index_file'] = $defaultIndexFile ? $defaultIndexFile : $nodefaultindex[0]['tmpl_path'];

        $pagedata['platform'] = $this->platform;
        return $this->singlepage('site/admin/theme/tmpl/frame.html', $pagedata);
    }

    protected function check($theme,&$msg='')
    {
        if(empty($theme)){
            $msg = app::get('site')->_('缺少参数');
            return false;
        }
        /** 权限校验 **/
        if($theme && preg_match('/[./]+/', $theme)){
            $msg = app::get('site')->_('非法操作');
            return false;
        }
        if(trim($theme)=='..')
        {
            $msg = app::get('site')->_('非法操作ddd');
            return false;
        }
        return true;
    }//End Function

    //flash上传
    public function swf_upload()
    {
        $pagedata['platform'] = $this->platform;
        $pagedata['ssid'] = kernel::single('base_session')->sess_id();
        $pagedata['swf_loc'] = app::get('desktop')->res_url;
        $pagedata['upload_max_filesize'] = kernel::single('site_theme_install')->ini_get_size('upload_max_filesize');
        return view::make('site/admin/theme/manage/swf_upload.html', $pagedata);
    }//End Function

    public function upload()
    {
        $themeInstallObj = kernel::single('site_theme_install');
        $res = $themeInstallObj->install($_FILES['Filedata'], $msg);
        if($res){
            $this->adminlog("上传模板", 1);
            $img = kernel::single('site_theme_file')->get_src($res['theme'],'preview.jpg');
            echo '<img src="'.$img.'" onload="$(this).zoomImg(50,50);" />';
        }else{
            echo $msg;
        }
    }//End Function

    public function set_default()
    {
        $this->begin();
        $theme = input::get('theme');
        if(!$this->check($theme,$msg))   $this->_error($msg);
        if($theme){
            if(kernel::single('site_theme_base')->set_default($this->platform, $theme)){
                $this->adminlog("设置默认模板[{$theme}]", 1);
                $this->end(true, app::get('site')->_('设置成功'));
            }else{
                $this->adminlog("设置默认模板[{$theme}]", 0);
                $this->end(false, app::get('site')->_('设置失败'));
            }
        }
    }//End Function

    public function bak() {
        $this->begin();
        $theme = input::get('theme');
        if(!$this->check($theme,$msg))   return $this->_error($msg);
        $data = kernel::single('site_theme_tmpl')->make_configfile($theme);

        if(kernel::single('site_theme_file')->bak_save($theme, $data)){
            $this->adminlog("备份模板[{$theme}]", 1);
            $this->end(true, app::get('site')->_('备份成功！'));
        }else{
            $this->adminlog("备份模板[{$theme}]", 0);
            $this->end(false, app::get('site')->_('备份失败！'));
        }
    }

    public function reset() {
        $this->begin();
        $theme = input::get('theme');
        $loadxml = input::get('rid');
        if(!$this->check($theme,$msg))  return $this->_error($msg);
        if(kernel::single("site_theme_install")->init_theme($theme, true, false, $loadxml)) {
            $this->adminlog("还原模板[{$theme}]", 1);
            $this->end(true, app::get('site')->_('还原成功！'));
        } else {
            $this->adminlog("还原模板[{$theme}]", 0);
            $this->end(false, app::get('site')->_('还原失败！'));
        }
    }

    public function remove_theme()
    {
        $this->begin();
        $get = input::get();
        foreach ((array)$get['theme'] as $theme){
            if(!$this->check($theme,$msg))  return  $this->_error($msg);
        }
        if(app::get('site')->model('themes')->remove_theme(array('theme'=>$get['theme']))){
            $this->adminlog("删除模板[{$get['theme']}]", 1);
            $this->end(true, app::get('site')->_('删除成功'), '?app=site&ctl=admin_theme_manage&act=index&platform='.$this->platform);
        }else{
            $this->adminlog("删除模板[{$get['theme']}]", 0);
            $this->end(false, app::get('site')->_('删除失败'));
        }
    }//End Function

    public function download()
    {
        $theme = input::get('theme');
        if(!$this->check($theme,$msg)) return $this->_error($msg);
        kernel::single('site_theme_tmpl')->output_pkg($theme);
        $this->adminlog("下载模板[{$theme}]", 1);
        exit;
    }//End Function

    public function cache_version()
    {
        $theme = input::get('theme');
        if(!$this->check($theme,$msg))  return $this->_error($msg);
        $this->begin();
        $this->end(kernel::single('site_theme_tmpl')->touch_theme_tmpl($theme));
    }//End Function

    public function maintenance()
    {
        $theme = input::get('theme');
        if (!$theme){
            if(is_dir(THEME_DIR)){
                kernel::single('site_theme_base')->maintenance_theme_files($this->platform, THEME_DIR);
            }
        }else{
            kernel::single('site_theme_base')->maintenance_theme_files($this->platform, $theme);
        }
        $this->adminlog("维护模板[{$theme}]", 1);
    }//End Function

}//End Class
