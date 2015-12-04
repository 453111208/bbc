<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class site_ctl_admin_explorer_app extends site_admin_controller
{
    /*
     * workground
     * @var string
     */
    var $workground = 'site_ctl_admin_explorer_app';

    /*
     * app::get('site')->_(验证是否可被编辑)
     * @param string $app_id
     * @param string $content_path
     * @return boolean
     *
     */
    private function check($app_id, $content_path)
    {
        $qb = app::get('site')->database()->createQueryBuilder();
        return $qb->select('id')->from('site_explorers')->where('app='.$qb->createPositionalParameter($app_id))->andWhere('path='.$qb->createPositionalParameter(str_replace('-', '/', $content_path)))->execute()->fetchColumn()!==false ? true : false;
    }//End Function

    /*
     * app::get('site')->_(app模版目录)
     */
    public function index()
    {
        return $this->finder('site_mdl_explorers', array(
            'title' => app::get('site')->_('APP资源管理'),
            'use_buildin_set_tag' => false,
            'use_buildin_export' => false,
        ));
    }//End Function

    /*
     * app::get('site')->_(目录浏览)
     */
    public function directory()
    {
        $app_id = input::get('app_id');
        $content_path = input::get('content_path');
        $open_path = trim(input::get('open_path'));

        if(!$this->check($app_id, $content_path))
        {
            return redirect::route('shopadmin', array('app' => 'site', 'ctl' => 'admin_explorer_app', 'act'=>'index'));
        }

        $fileObj = kernel::single('site_explorer_file');
        $dir = realpath(APP_DIR . '/' . $app_id . '/' . str_replace('-', '/', $content_path) . '/' . str_replace(array('-','.'), array('/','/'), $open_path));   //open_pathapp::get('site')->_(不允许有)'./'&'../'
        $filter=array(
                 'id' => $app_id,
                 'dir' => $dir,
                 'show_bak' => false,
                 'type' => 'all'
             );
        $file = $fileObj->file_list($filter);
        $file = $fileObj->parse_filter($file);
        $pagedata['file'] = array_reverse($file);
        $pagedata['url'] = sprintf('?app=%s&ctl=%s&act=%s&app_id=%s&content_path=%s',
            input::get('app'),
            input::get('ctl'),
            input::get('act'),
            input::get('app_id'),
            input::get('content_path')
        );
        $pagedata['app_id'] = $app_id;
        $pagedata['content_path'] = $content_path;
        $pagedata['open_path'] = $open_path;
        $pagedata['last_path'] = strrpos($open_path, '-') ? substr($open_path, 0, strrpos($open_path, '-')) : ($open_path ? ' ' : '');
        return $this->page('site/admin/explorer/app/directory.html', $pagedata);
    }//End Function

    /*
     * app::get('site')->_(文件详情)
     */
    public function detail()
    {
        $app_id = input::get('app_id');
        $content_path = input::get('content_path');
        $open_path = input::get('open_path');
        $file_name = input::get('file_name');

        if(!$this->check($app_id, $content_path))   $this->_error();
        $fileObj = kernel::single('site_explorer_file');
        $dir = realpath(APP_DIR . '/' . $app_id . '/' . str_replace('-', '/', $content_path) . '/' . str_replace(array('-','.'), array('/','/'), $open_path));   //open_pathapp::get('site')->_(不允许有)'./'&'../'
        $filter=array(
                 'id' => $app_id,
                 'dir' => $dir,
                 'show_bak' => true,
                 'type' => 'all'
             );
        $filenameInfo = pathinfo($file_name);
        $pagedata['file_baklist'] = $fileObj->get_file_baklist($filter, $file_name);
        $pagedata['app_id'] = $app_id;
        $pagedata['content_path'] = $content_path;
        $pagedata['open_path'] = $open_path;
        $pagedata['file_name'] = $file_name;
        if(in_array($filenameInfo['extension'], array('css', 'html', 'js', 'xml'))){
            $pagedata['file_content']  = file_get_contents($dir . '/' . $file_name);
            return view::make('site/admin/explorer/app/tpl_source.html', $pagedata);
        }else{
            $pagedata['file_url'] = kernel::base_url(1) .  rtrim(str_replace('//', '/', '/app/' . $app_id . '/' . str_replace('-', '/', $content_path) . '/' . str_replace(array('-','.'), array('/','/'), $open_path) . '/' . $file_name));
            return view::make('site/admin/explorer/app/tpl_image.html', $pagedata);
        }
    }//End Function

    /*
     * app::get('site')->_(保存文件)
     */
    public function svae_source()
    {
        $this->begin();
        $app_id = input::get('app_id');
        $content_path = input::get('content_path');
        $open_path = input::get('open_path');
        $file_name = input::get('file_name');

        if(!$this->check($app_id, $content_path))   $this->_error();

        $has_bak = (input::get('has_bak')) ? true : false;
        $file_source = input::get('file_source');

        $fileObj = kernel::single('site_explorer_file');
        $dir = realpath(APP_DIR . '/' . $app_id . '/' . str_replace('-', '/', $content_path) . '/' . str_replace(array('-','.'), array('/','/'), $open_path));   //open_pathapp::get('site')->_(不允许有)'./'&'../'
        if($has_bak){
            $fileObj->backup_file($dir . '/' . $file_name);
        }
        $fileObj->save_source($dir . '/' . $file_name, $file_source);
        $this->adminlog("修改模板文件源码[{$file_name}]", 1);
        $this->end(true, app::get('site')->_('保存成功'));
    }//End Function

    /*
     *app::get('site')->_( 保存图片文件)
     */
    public function save_image()
    {
        $this->begin();
        $app_id = input::get('app_id');
        $content_path = input::get('content_path');
        $open_path = input::get('open_path');
        $file_name = input::get('file_name');

        if(!$this->check($app_id, $content_path))   $this->_error();

        $has_bak = (input::get('has_bak')) ? true : false;

        $fileObj = kernel::single('site_explorer_file');
        $dir = realpath(APP_DIR . '/' . $app_id . '/' . str_replace('-', '/', $content_path) . '/' . str_replace(array('-','.'), array('/','/'), $open_path));   //open_pathapp::get('site')->_(不允许有)'./'&'../'
        if($has_bak){
            $fileObj->backup_file($dir . '/' . $file_name);
        }
        $fileObj->save_image($dir . '/' . $file_name, $_FILES['upfile']);
        $this->adminlog("修改模板图片[{$file_name}]", 1);
        $this->end(true, app::get('site')->_('保存成功'));
    }//End Function

    /*
     * app::get('site')->_(删除文件)
     */
    public function delete_file()
    {
        $this->begin();
        $app_id = input::get('app_id');
        $content_path = input::get('content_path');
        $open_path = input::get('open_path');
        $file_name = input::get('file_name');

        if(!$this->check($app_id, $content_path))   $this->_error();

        $dir = realpath(APP_DIR . '/' . $app_id . '/' . str_replace('-', '/', $content_path) . '/' . str_replace(array('-','.'), array('/','/'), $open_path));
        $fileObj = kernel::single('site_explorer_file');
        $fileObj->delete_file($dir . '/' . $file_name);
        $this->adminlog("删除模板文件[{$file_name}]", 1);
        $this->end(true, app::get('site')->_('删除成功'));
    }//End Function

    /*
     * app::get('site')->_(恢复文件)
     */
    public function recover_file()
    {
        $this->begin();
        $app_id = input::get('app_id');
        $content_path = input::get('content_path');
        $open_path = input::get('open_path');
        $file_name = input::get('file_name');

        if(!$this->check($app_id, $content_path))   $this->_error();

        $dir = realpath(APP_DIR . '/' . $app_id . '/' . str_replace('-', '/', $content_path) . '/' . str_replace(array('-','.'), array('/','/'), $open_path));

        $fileObj = kernel::single('site_explorer_file');
        $fileObj->recover_file($dir . '/' . $file_name);
        $this->adminlog("恢复模板文件[{$file_name}]", 1);
        $this->end(true, app::get('site')->_('恢复成功'));
    }//End Function

}//End Class
