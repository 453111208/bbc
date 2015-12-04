<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class site_ctl_admin_explorer_theme extends site_admin_controller
{
    /*
     * workground
     * @var string
     */
    var $workground = 'site.wrokground.theme';

    private function get_theme_dir($theme, $open_path='')
    {
        return ecos_cactus('site','theme_get_theme_dir', $theme, $open_path);
    }//End Function

    /*
     * 目录浏览
     */
    public function directory()
    {
        $theme = input::get('theme');
        $open_path = input::get('open_path');
        $is_part = input::get('part');

        $pagedata['url'] = sprintf('?app=%s&ctl=%s&act=%s&theme=%s',
                                   input::get('app'),
                                   input::get('ctl'),
                                   $act?$act:input::get('act'),
                                   input::get('theme')
        );        
        $pagedata['file'] =$this->get_directory_body($theme,$open_path,'');

        $pagedata['theme'] = $theme;
        $pagedata['list'] = kernel::single('site_theme_tmpl')->get_edit_list($theme);
        foreach ((array)$pagedata['list'] as $k=>$list){
            foreach ($list as $key=>$li){
                if (!$li||!$li['tmpl_name']) continue;
                $file_name = THEME_DIR. '/' . $theme . '/'.$li['tmpl_name'];

                if (filesize($file_name)) continue;
                unset($pagedata['list'][$k][$key]);
            }
        }
        $pagedata['types'] = kernel::single('site_theme_tmpl')->get_name($this->platform);
        $pagedata['open_path'] = $open_path;
        $pagedata['last_path'] = strrpos($open_path, '-') ? substr($open_path, 0, strrpos($open_path, '-')) : ($open_path ? ' ' : '');
        $pagedata['pagehead_active'] = 'source';

        //设置可视化编辑页面（默认and非默认）
        $defaultIndexFile = kernel::single('site_theme_tmpl')->get_default('index',$theme);
        $nodefaultindex = $this->app->model('themes_tmpl')->getList('tmpl_path',array('theme'=>$theme,'tmpl_type'=>'index'));
        $pagedata['current']['default_index_file'] = $defaultIndexFile ? $defaultIndexFile : $nodefaultindex[0]['tmpl_path'];
        
        $pagedata['platform'] = $this->platform;
        if (!$open_path&&!$is_part){
            return $this->singlepage('site/admin/explorer/theme/directory.html', $pagedata);
        }else{
            return view::make('site/admin/explorer/theme/theme_directory_body.html', $pagedata);
         }
    }//End Function

    /**
     * 获取目录树的主题
     */
    private function get_directory_body($theme='',$open_path='',$msg='',$act='')
    {
        /** 加入目录限制 **/
        if(!$this->check($theme,$msg))   $this->_error($msg);

        $fileObj = kernel::single('site_explorer_file');
        $fileObj->set_theme($theme);
        $dir = $this->get_theme_dir($theme, $open_path);
        $filter=array(
                 'id' => $atheme,
                 'dir' => $dir,
                 'show_bak' => false,
                 'type' => 'all'
             );
        $file = $fileObj->file_list($filter);

        $file = $fileObj->parse_filter($file);
        return array_reverse($file);
    }

    /*
     * 文件详情
     */
    public function detail()
    {
        $theme = input::get('theme');
        $open_path = input::get('open_path');
        $file_name = input::get('file_name');
        if(!$this->check($theme,$msg))   $this->_error($msg);

        $fileObj = kernel::single('site_explorer_file');
        $fileObj->set_theme($theme);
        $dir = $this->get_theme_dir($theme, $open_path);
        $file_name = trim($file_name);
        $get_file = $dir . '/' . $file_name;

        $filter=array(
                 'id' => $theme,
                 'dir' => $dir,
                 'show_bak' => true,
                 'type' => 'all'
             );
        $filenameInfo = pathinfo($file_name);
        $pagedata['file_baklist'] = $fileObj->get_file_baklist($filter, $file_name);
        $pagedata['theme'] = $theme;
        $pagedata['open_path'] = $open_path;
        $pagedata['file_name'] = $file_name;
        $file_content = $fileObj->get_file($get_file);
        if(in_array($filenameInfo['extension'], array('css', 'html', 'js', 'xml'))){
            $pagedata['file_content']  = $file_content;

            if($filenameInfo['extension']=='js')
            {
                $filenameInfo['extension'] = 'javascript';
            }
            $pagedata['mode'] = 'text/'.$filenameInfo['extension'];/*php mode: application/x-httpd-php */
            return view::make('site/admin/explorer/theme/tpl_source.html', $pagedata);
        }else{
            $pagedata['file_url'] = kernel::base_url(1) . rtrim(str_replace('//', '/', '/themes/' . $theme . '/' . str_replace(array('-','.'), array('/','/'), $open_path) . '/' . $file_name));

            return view::make('site/admin/explorer/theme/tpl_image.html', $pagedata);
        }
    }//End Function

    /*
     * 保存文件
     */
    public function svae_source()
    {
        $this->begin();
        $theme = input::get('theme');
        $open_path = input::get('open_path');
        $file_name = input::get('file_name');

        if(!$this->check($theme,$msg))   $this->_error($msg);

        $has_bak = (input::get('has_bak')) ? true : false;
        $has_clearcache = (input::get('has_clearcache')) ? true : false;
        $file_source = input::get('file_source');

        $fileObj = kernel::single('site_explorer_file',$theme);
        $fileObj->set_theme($theme);
        $dir = $this->get_theme_dir($theme, $open_path);

        $get_file = $dir . '/' . $file_name;

        if($has_bak){
            $fileObj->backup_file($get_file);
        }
        $fileObj->save_source($get_file, $file_source);
        if($has_clearcache){
            @touch($dir . '/' . $file_name);
            kernel::single('site_theme_base')->set_theme_cache_version($theme);
        }
        $this->adminlog("编辑模板文件源码[{$file_name}]", 1);
        $this->end(true, app::get('site')->_('保存成功'));
    }//End Function

    /*
     * 保存图片文件
     */
    public function save_image()
    {
        $this->begin();
        $theme = input::get('theme');
        $open_path = input::get('open_path');
        $file_name = input::get('file_name');

        if(!$this->check($theme,$msg))   $this->_error($msg);

        $has_bak = (input::get('has_bak')) ? true : false;

        $fileObj = kernel::single('site_explorer_file',$theme);
        $dir = $this->get_theme_dir($theme, $open_path);

        $get_file = $dir . '/' . $file_name;

        if($has_bak){
            $fileObj->backup_file($get_file);
        }
        $file_name = $dir . '/' . $file_name;
        $fileObj->save_image($file_name, $_FILES['upfile']);
        $this->adminlog("修改模板图片[{$file_name}]", 1);
        $this->end(true, app::get('site')->_('保存成功'));
    }//End Function

    /*
     * 删除文件
     */
    public function delete_file()
    {
        $this->begin();
        $theme = input::get('theme');
        $open_path = input::get('open_path');
        $file_name = input::get('file_name');

        if(!$this->check($theme,$msg))   $this->_error($msg);

        $dir = $this->get_theme_dir($theme, $open_path);
        $fileObj = kernel::single('site_explorer_file',$theme);
        $file_name = $dir . '/' . trim($file_name);
        $fileObj->delete_file($file_name);
        $this->adminlog("删除模板文件[{$file_name}]", 1);
        $this->end(true, app::get('site')->_('删除成功'));
    }//End Function

    /*
     * 恢复文件
     */
    public function recover_file()
    {
        $this->begin();
        $theme = input::get('theme');
        $open_path = input::get('open_path');
        $file_name = input::get('file_name');

        if(!$this->check($theme,$msg))   $this->_error($msg);

        $dir = $this->get_theme_dir($theme, $open_path);
        $fileObj = kernel::single('site_explorer_file',$theme);

        $file_name = $dir . '/' . $file_name;
        $fileObj->recover_file($file_name);
        $this->adminlog("恢复模板文件[{$file_name}]", 1);
        $this->end(true, app::get('site')->_('恢复成功'));
    }//End Function

}//End Class
