<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class site_ctl_admin_theme_widget extends site_admin_controller
{

    /*
     * workground
     * @var string
     */
    var $workground = 'site.wrokground.theme';

    public function editor()
    {
        $theme = input::get('theme');
        $file = input::get('file');
        $urlType = input::get('platform');

        header('Content-Type: text/html; charset=utf-8');
        $this->path[] = array('text'=>app::get('site')->_('模板可视化编辑'));
        $pagedata['views'] = kernel::single('site_theme_base')->get_view($theme);
        $pagedata['widgetsLib'] = kernel::single('site_theme_widget')->get_libs($theme);

        $pagedata['list'] = kernel::single('site_theme_tmpl')->get_edit_list($theme);
        $pagedata['types'] = kernel::single('site_theme_tmpl')->get_name($this->platform);

        $pagedata['theme'] = $theme;
        $pagedata['view'] = $file;
        $pagedata['viewname'] = kernel::single('site_theme_tmpl')->get_list_name($this->platform, $file);

        $pagedata['shopadmin'] = url::route('shopadmin');

        $pagedata['site_url'] = url::route('topc');
        $pagedata['pagehead_active'] = 'preview';
        $pagedata['save_url'] = url::route('shopadmin', ['app' => 'site', 'ctl' => 'admin_theme_widget', 'act' => 'do_preview']);
       // $pagedata['preview_url'] = url::route('topc');
        if($urlType=='pc')
        {
            $pagedata['preview_url'] = url::route('topc');
        }
        if($urlType=='wap')
        {
            $pagedata['preview_url'] = url::route('topm');
        }
        //设置编辑默认页面
        $defaultIndexFile = kernel::single('site_theme_tmpl')->get_default('index',$theme);
        $nodefaultindex = $this->app->model('themes_tmpl')->getList('tmpl_path',array('theme'=>$theme,'tmpl_type'=>'index'));
        $pagedata['current']['default_index_file'] = $defaultIndexFile ? $defaultIndexFile : $nodefaultindex[0]['tmpl_path'];

        $pagedata['platform'] = $this->platform;
        return $this->singlepage('site/admin/theme/widget/editor.html', $pagedata);
    }//End Function

    public function preview()
    {
        $themeName = input::get('theme');
        $layout = input::get('file');
        /** 清空widgets数据缓存 **/
        if ($_SESSION['WIDGET_TMP_DATA'][$themeName.'/'.$file]) $_SESSION['WIDGET_TMP_DATA'][$themeName.'/'.$file] = array();
        if ($_SESSION['WIDGET_TMP_DATA'][$themeName.'/block/header.html']) $_SESSION['WIDGET_TMP_DATA'][$themeName.'/block/header.html'] = array();
        if ($_SESSION['WIDGET_TMP_DATA'][$themeName.'/block/footer.html']) $_SESSION['WIDGET_TMP_DATA'][$themeName.'/block/footer.html'] = array();

        header('Content-Type: text/html; charset=utf-8');
        kernel::single('base_session')->close();
        $compiler = view::getEngine()->getCompiler();
        $compiler->loadCompileHelper(kernel::single('site_theme_complier'));
        $compiler->loadViewHelper(kernel::single('site_theme_helper'));
        $theme = theme::uses($themeName)->preview();
        return $theme->layout($layout)->render();

    }//End Function

    public function add_widgets_page()
    {
        $theme = input::get('theme');
        $pagedata['theme'] = $theme;
        $pagedata['widgetsLib'] = kernel::single('site_theme_widget')->get_libs($theme);
        $theme_url = kernel::get_themes_host_url().'/'.$theme;
        $app_base_url = kernel::get_app_statics_host_url();

        if ($pagedata['widgetsLib']['usual'])
        {
            foreach((array)$pagedata['widgetsLib']['usual'] as $key=>$widgets)
            {
                if ($widgets['theme'])
                {
                    if (file_exists(THEME_DIR.'/'.$theme.'/widgets/'.$widgets['name'].'/images/icon.jpg')) {
                        $pagedata['widgetsLib']['usual'][$key]['img'] = $theme_url.'/widgets/'.$widgets['name'].'/images/icon.jpg';
                    }else{
                        $pagedata['widgetsLib']['usual'][$key]['img'] = $this->app->res_url.'/images/widgets/icon.jpg';
                    }

                    if (file_exists(THEME_DIR.'/'.$theme.'/widgets/'.$widgets['name'].'/images/preview.jpg')) {
                        $pagedata['widgetsLib']['usual'][$key]['bimg'] = $theme_url.'/widgets/'.$widgets['name'].'/images/preview.jpg';
                    }else{
                        $pagedata['widgetsLib']['usual'][$key]['bimg'] = $this->app->res_url.'/images/widgets/widget.jpg';
                    }
                }
            }
        }

        return view::make('site/admin/theme/widget/add_widgets_page.html', $pagedata);
    }//End Function

    public function add_widgets_page_extend()
    {
        $theme = input::get('theme');
        $type = input::get('type');
        $catalog = input::get('catalog');

        $pagedata['theme'] = $theme;
        $pagedata['widgetsLib'] = kernel::single('site_theme_widget')->get_libs_extend($theme, $catalog);
        $app_base_url = kernel::get_app_statics_host_url();
        $theme_url = kernel::get_themes_host_url().'/'.$theme;

        if ($pagedata['widgetsLib']['list'])
        {
            foreach((array)$pagedata['widgetsLib']['list'] as $key=>$widgets)
            {
                if ($widgets['theme'])
                {
                    if (file_exists(THEME_DIR.'/'.$theme.'/widgets/'.$widgets['name'].'/images/icon.jpg')) {
                        $pagedata['widgetsLib']['list'][$key]['img'] = $theme_url.'/widgets/'.$widgets['name'].'/images/icon.jpg';
                    }else{
                        $pagedata['widgetsLib']['list'][$key]['img'] = $this->app->res_url.'/images/widgets/icon.jpg';

                    }

                    if (file_exists(THEME_DIR.'/'.$theme.'/widgets/'.$widgets['name'].'/images/preview.jpg')) {
                        $pagedata['widgetsLib']['list'][$key]['bimg'] = $theme_url.'/widgets/'.$widgets['name'].'/images/preview.jpg';
                    }else{
                        $pagedata['widgetsLib']['list'][$key]['bimg'] = $this->app->res_url.'/images/widgets/widget.jpg';
                    }
                }
            }
        }

        return view::make('site/admin/theme/widget/add_widgets_page_extend.html', $pagedata);
    }//End Function

    public function get_widgets_info()
    {
        $type = input::get('type');
        $widgets = input::get('widgets');
        $widgets_app = input::get('widgets_app');
        $widgets_theme = input::get('widgets_theme');
        if($widgets){
            $pagedata['widgetsInfo'] = kernel::single('site_theme_widget')->get_this_widgets_info($widgets, $widgets_app, $widgets_theme);
            $pagedata['widgets'] = $widgets;

        }
        $pagedata['theme'] = app::get('site')->getConf('current_theme');
        return view::make('site/admin/theme/widget/get_widgets_info.html', $pagedata);
    }//End Function

    public function do_add_widgets(){

        $widgets = input::get('widgets');
        $widgets_app = input::get('widgets_app');
        $widgets_theme = input::get('widgets_theme');
        $theme = input::get('theme');
        $pagedata['widget_editor'] = kernel::single('site_theme_widget')->editor($widgets, $widgets_app, $widgets_theme, $theme);

        $pagedata['widgets_type'] = $widgets;
        $pagedata['widgets_app'] = $widgets_app;
        $pagedata['widgets_theme'] = $widgets_theme;
        $pagedata['theme'] = $theme;

        $pagedata['i']=is_array($_SESSION['_tmp_wg_insert'])?count($_SESSION['_tmp_wg_insert']):0;
        $pagedata['basic_config'] = kernel::single('site_theme_base')->get_basic_config($theme);

        return view::make('site/admin/theme/widget/do_add_widgets.html', $pagedata);
    }

    public function do_edit_widgets(){

//        header("Cache-Control:no-store, no-cache, must-revalidate"); //强制刷新IE缓存
        $widgets_id = input::get('widgets_id');
        $theme = input::get('theme');

        if(is_numeric($widgets_id)){
            $widgetObj = app::get('site')->model('widgets_instance')->getList('*', array('widgets_id'=>$widgets_id));
            $widgetObj = $widgetObj[0];
        }elseif(preg_match('/^tmp_([0-9]+)$/i',$widgets_id,$match)){
            $widgetObj = $_SESSION['_tmp_wg_insert'][$match[1]];
        }

        $pagedata['widget_editor'] = kernel::single('site_theme_widget')->editor($widgetObj['widgets_type'],$widgetObj['app'],$widgetObj['theme'],$theme,$widgetObj['params']);
        $pagedata['widgets_type'] = $widgetObj['widgets_type'];

         $pagedata['widgetsTpl'] = str_replace('\'','\\\'',kernel::single('site_theme_widget')->admin_wg_border(array('title'=>$widgetObj['title'],'html'=>'loading...'),$theme));


        $pagedata['widgets_id'] = $widgets_id;
        $pagedata['widgets_title'] = $widgetObj['title'];
        $pagedata['widgets_classname']=$widgetObj['classname'];
        $pagedata['widgets_domid']=$widgetObj['domid'];
        $pagedata['widgets_app'] = $widgetObj['app'];
        $pagedata['widgets_theme'] = $widgetObj['theme'];

        $pagedata['widgets_tpl']=$widgetObj['tpl'];


        $pagedata['theme'] = $theme;
        $pagedata['basic_config'] = kernel::single('site_theme_base')->get_basic_config($theme);
        return view::make('site/admin/theme/widget/do_edit_widgets.html', $pagedata);
    }

    public function insert_widget(){

        header('Content-Type: text/html;charset=utf-8');

        $widgets = input::get('widgets');
        $widgets_app = input::get('widgets_app');
        $widgets_theme = input::get('widgets_theme');
        $theme = input::get('theme');
        $domid = input::get('domid');

        $wg = input::get('__wg');

        $set = array(
            'widgets_type' => $widgets,
            'app' => $widgets_app,
            'theme' => $widgets_theme,
            'title' => $wg['title'],
            'tpl' => $wg['tpl'],
            'domid' => $wg['domid']?$wg['domid']:$domid,
            'classname' => $wg['classname'],
        );

        $post = input::get();
        unset($post['__wg']);

        $set['params'] = $post;
        $set['_domid'] = $set['domid'];

        $i=is_array($_SESSION['_tmp_wg_insert'])?count($_SESSION['_tmp_wg_insert']):0;
        $_SESSION['_tmp_wg_insert'][$i] = $set;
        $data = kernel::single('site_theme_widget')->admin_wg_border(
            array(  'title'=>$set['title'],
                    'domid'=>$set['domid'],
                    'widgets_type'=>$set['widgets_type'],
                    'html'=> kernel::single('site_theme_widget')->fetch($set, true),
            ),
            $theme,true);
        $theme_url = kernel::get_themes_host_url().'/'.$theme;
        $data = str_replace('%THEME%', $theme_url, $data);
        echo $data;
    }

    public function save_widget()
    {
        header('Content-Type: text/html;charset=utf-8');

        $widgets_id = input::get('widgets_id');
        $widgets = input::get('widgets');
        $widgets_app = input::get('widgets_app');
        $widgets_theme = input::get('widgets_theme');
        $theme = input::get('theme');
        $domid = input::get('domid');

        $wg = input::get('__wg');

        if($widgets_type=='html')   $widgets_type='usercustom';
        $set = array(
            'widgets_type'=>$widgets,
            'app' => $widgets_app,
            'theme' => $widgets_theme,
            'title' => $wg['title'],
            'tpl' => $wg['tpl'],
            'domid' => $wg['domid']?$wg['domid']:$domid,
            'classname' => $wg['classname'],
        );

        $post = input::get();
        unset($post['__wg']);

        $set['params'] = $post;
        $set['_domid'] = $set['domid'];

        if(is_numeric($widgets_id)){
            $sdata = $set;
            kernel::single('site_theme_widget')->save_widgets($widgets_id, $sdata);
            $set['widgets_id'] = $widgets_id;
        $_SESSION['_tmp_wg_update'][$widgets_id] = $set;
        }elseif(preg_match('/^tmp_([0-9]+)$/i',$widgets_id,$match)){
            $_SESSION['_tmp_wg_insert'][$match[1]] = $set;
        }

        $data = kernel::single('site_theme_widget')->admin_wg_border(
            array(  'widgets_id'=>$widgets_id,
                    'title'=>$set['title'],
                    'domid'=>$set['domid'],
                    'widgets_type'=>$set['widgets_type'],
                    'html'=> kernel::single('site_theme_widget')->fetch($set, true),
            ),
            $theme,true);
        $theme_url = kernel::get_themes_host_url().'/'.$theme;
        $data = str_replace('%THEME%', $theme_url, $data);
        $this->adminlog("添加挂件[{$theme}:{$widgets_theme}]", 1);
        echo $data;
    }//End Function


    public function do_preview()
    {
        $widgets = input::get('widgets');
        $html = input::get('html');
        $files = input::get('files');

        if(is_array($widgets)){

            foreach($widgets as $widgets_id=>$base){
                $aTmp=explode(':',$base);
                $base_id=array_pop($aTmp);
                $base_slot=array_pop($aTmp);
                $base_file=implode(':',$aTmp);
                if($html[$widgets_id]){
                    $widgetsSet[$widgets_id] = array(
                        'core_file'=>$base_file,
                        'core_slot'=>$base_slot,
                        'core_id'=>$base_id,
                        'params'=>array('html'=>stripslashes($html[$widgets_id]))
                    );
                }else{
                    $widgetsSet[$widgets_id] = array('core_file'=>$base_file,'core_slot'=>$base_slot,'core_id'=>$base_id);
                }
            }
        }

        if(false !== ($map = kernel::single('site_theme_widget')->save_preview_all($widgetsSet,$files))){
            setcookie('site[preview]', 'true', 0, kernel::base_url() . '/');
            $map = array(
                'success'=>true
            );
            echo json_encode($map);
        }else{
            echo json_encode(false);
        }
    }//End Function

    public function save_all()
    {
        $widgets = input::get('widgets');
        $html = input::get('html');
        $files = input::get('files');

        if(is_array($widgets)){

            foreach($widgets as $widgets_id=>$base){
                $aTmp=explode(':',$base);
                $base_id=array_pop($aTmp);
                $base_slot=array_pop($aTmp);
                $base_file=implode(':',$aTmp);
                if($html[$widgets_id]){
                    $widgetsSet[$widgets_id] = array(
                        'core_file'=>$base_file,
                        'core_slot'=>$base_slot,
                        'core_id'=>$base_id,
                        'params'=>array('html'=>stripslashes($html[$widgets_id]))
                    );
                }else{
                    $widgetsSet[$widgets_id] = array('core_file'=>$base_file,'core_slot'=>$base_slot,'core_id'=>$base_id);
                }
            }
        }

        if(false !== ($map = kernel::single('site_theme_widget')->save_all($widgetsSet,$files))){
            $this->adminlog("可视化编辑保存模板", 1);
            echo json_encode($map);
        }else{
            $this->adminlog("可视化编辑保存模板", 0);
            echo json_encode(false);
        }
    }//End Function

}//End Class
