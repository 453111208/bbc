<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class site_ctl_admin_theme_tmpl extends site_admin_controller
{

    /*
     * workground
     * @var string
     */
    var $workground = 'site.wrokground.theme';

    private function get_theme_dir($theme, $open_path='')
    {
        return realpath(THEME_DIR . '/' . $theme . '/' . str_replace(array('-','.'), array('/','/'), $open_path));
    }

    public function index()
    {
        $theme = input::get('theme');
        $pagedata['list'] = kernel::single('site_theme_tmpl')->get_edit_list($theme);
        $pagedata['types'] = kernel::single('site_theme_tmpl')->get_name($this->platform);
        $pagedata['theme'] = $theme;
        $pagedata['platform'] = $this->platform;

        return view::make('site/admin/theme/tmpl/index.html', $pagedata);
    }

    public function add()
    {
        $theme = input::get('theme');
        if(!$this->check($theme,$msg))  return  $this->_error($msg);

        $pagedata['theme'] = $theme;
        $pagedata['type'] = input::get('type')?input::get('type'):'index';
        $pagedata['types'] = kernel::single('site_theme_tmpl')->get_name($this->platform);

        $pagedata['content'] = ecos_cactus('site','theme_get_source_code', $theme, $pagedata['type']);
        $pagedata['platform'] = $this->platform;
        return view::make('site/admin/theme/tmpl/add.html', $pagedata);
    }

    public function add_source_page()
    {
        $theme = input::get('theme');
        if(!$this->check($theme,$msg))  return  $this->_error($msg);

        $pagedata['theme'] = $theme;
        $pagedata['type'] = input::get('type');
        $pagedata['types'] = kernel::single('site_theme_tmpl')->get_name($this->platform);

        $pagedata['content'] = ecos_cactus('site','theme_get_source_code',$theme,$pagedata['type']);

        return view::make('site/admin/theme/tmpl/add_resource.html', $pagedata);
    }

    public function set_default()
    {
        $this->begin();
        $id = input::get('id');
        if($id > 0 && is_numeric($id)){
            $data = $this->app->model('themes_tmpl')->getList('*', array('id'=>$id));
            $data = $data[0];
            if($data['id']){
                kernel::single('site_theme_tmpl')->set_default($data['tmpl_type'], $data['theme'], $data['tmpl_path']);
                $this->adminlog("设置模板默认文件[{$data['theme']}:{$data['tmpl_path']}]", 1);
                $this->end(true, app::get('site')->_('设置成功'));
            }
        }else {
            $this->adminlog("设置模板默认文件[{$data['theme']}:{$data['tmpl_path']}]", 0);
            $this->end(false, app::get('site')->_('设置失败'));
        }
    }

    /*
     * 添加模版
     */
    public function insert_tmpl()
    {
        $this->begin();
        $data['theme'] = input::get('theme');
        if(!$this->check($data['theme'],$msg))  return $this->_error($msg);

        $data['tmpl_type'] = input::get('tmpl_type');
        $data['tmpl_name'] = input::get('tmpl_name');
        $data['tmpl_path'] = input::get('tmpl_path');
        $data['content'] = input::get('content');

        if(kernel::single('site_theme_tmpl')->insert_tmpl($data,$msg)){
            $this->adminlog("添加模板文件[{$data['theme']}:{$data['tmpl_path']}:{$data['tmpl_name']}]", 1);
            $this->end(true, $msg);
        }else{
            $this->adminlog("添加模板文件[{$data['theme']}:{$data['tmpl_path']}:{$data['tmpl_name']}]", 0);
            $this->end(false, $msg);
        }
    }//End Function

    /*
     * 添加相似
     */
    public function copy_tmpl()
    {
        $this->begin();
        $theme = input::get('theme');
        $file_name = input::get('tmpl');

        if(!$this->check($theme,$msg))   $this->end(false, $msg);
        $tmpl = kernel::single('site_theme_tmpl');
        $result = $tmpl->copy_tmpl($file_name, $theme);
        if($result){
            $this->adminlog("添加相似模板文件[{$theme}:{$file_name}]", 1);
            $this->end(true, app::get('site')->_('添加成功'));
        }else{
            $this->adminlog("添加相似模板文件[{$theme}:{$file_name}]", 0);
            $this->end(false, app::get('site')->_('添加失败'));
        }
    }

    /*
     * 删除模版文件
     */
    public function delete_tmpl()
    {
        $this->begin();
        $theme = input::get('theme');
        $file_name = input::get('tmpl');

        if(!$this->check($theme,$msg))   $this->end(false,$msg);

        //数据库
        if(kernel::single('site_theme_tmpl')->delete_tmpl($file_name, $theme)){

            //物理
            $dir = $this->get_theme_dir($theme, '/');
            $fileObj = kernel::single('site_explorer_file',$theme);
            $fileObj->delete_file($dir . '/' . $file_name);

            $filter=array(
                     'id' => $theme,
                     'dir' => $dir,
                     'show_bak' => true,
                     'type' => 'all'
                 );
            $file_baklist = $fileObj->get_file_baklist($filter, $file_name);
            if(is_array($file_baklist)){
                foreach($file_baklist AS $fileinfo){
                    $fileObj->delete_file($dir . '/' . $fileinfo['name']);
                }
            }
            $this->adminlog("删除模板文件[{$theme}:{$file_name}]", 1);
            $this->end(true, app::get('site')->_('删除成功'));
        }else{
            $this->adminlog("删除模板文件[{$theme}:{$file_name}]", 1);
            $this->end(false,app::get('site')->_('删除失败'));
        }
    }

}
