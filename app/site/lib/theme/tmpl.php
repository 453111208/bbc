<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class site_theme_tmpl
{

    public function __construct()
    {
        $this->themesdir = array('wap'=>WAP_THEME_DIR, 'pc'=>THEME_DIR);
    }

    public function get_default($type, $theme)
    {
        return app::get('site')->getConf('custom_template_'.$theme.'_'.$type);
    }

    public function set_default($type, $theme, $value)
    {
        return app::get('site')->setConf('custom_template_'.$theme.'_'.$type, $value);
    }

    public function del_default($type, $theme)
    {
        return app::get('site')->setConf('custom_template_'.$theme.'_'.$type, '');
    }

    public function set_all_tmpl_file($theme)
    {
        $qb = app::get('site')->database()->createQueryBuilder();
        if ($tmplPath = $qb->select('tmpl_path')->from('site_themes_tmpl')->where('theme='.$qb->createPositionalParameter($theme))->execute()->fetchColumn())
        {
            return app::get('site')->setConf('custom_template_'.$theme.'_all_tmpl', $tmplPath);
        }
        throw new \OutOfBoundsException("cannot find theme:{$theme}");
    }

    public function get_all_tmpl_file($theme)
    {
        return app::get('site')->getConf('custom_template_'.$theme.'_all_tmpl');
    }

    public function tmpl_file_exists($tmpl_file, $theme)
    {
        $all = $this->get_all_tmpl_file($theme);
        $all[] = 'block/header.html';
        $all[] = 'block/footer.html';   //头尾文件
        if(is_array($all)){
            return in_array($tmpl_file, $all);
        }else{
            return false;
        }
    }

    public function get_edit_list($theme)
    {
        $data = app::get('site')->model('themes_tmpl')->getList('*', array("theme"=>$theme));
        if(is_array($data)){
            foreach($data AS $value){
                if($this->get_default($value['tmpl_type'], $theme) == $value['tmpl_path'])
                    $value['default'] = 1;

                $ret[$value['tmpl_type']][] = $value;
            }
        }
        return $ret;
    }


    public function install($platform, $theme)
    {
        $list = array();
        $this->__get_all_files(THEME_DIR . '/' . $theme, $list, false);
        $ctl = $this->get_name($platform);
        foreach($list AS $key=>$value){
            $file_name = basename($value, '.html');
            if(!strpos($file_name,'.')){
                if(($pos=strpos($file_name,'-'))){
                    $type=substr($file_name,0,$pos);
                    $file[$type][$key]['name']=$ctl[substr($file_name,0,$pos)];
                    $file[$type][$key]['file']=$file_name.'.html';
                }else{
                    $type=$file_name;
                    $file[$file_name][$key]['name']=$ctl[$file_name];
                    $file[$file_name][$key]['file']=$file_name.'.html';
                    //$file[$key]['name']=$ctl[$file_name];
                }

                touch(THEME_DIR . '/' . $theme . '/' . $file_name . '.html');

                if($type && array_key_exists($type, $ctl)){
                    $array = array(
                        'platform'  => $platform,
                        'theme'     => $theme,
                        'tmpl_type' => $type,
                        'tmpl_name' => $file_name.'.html',
                        'tmpl_path' => $file_name.'.html',
                        'version'   => filemtime(THEME_DIR . '/' . $theme . '/' . $file_name . '.html'),
                        'content'   => file_get_contents(THEME_DIR . '/' . $theme . '/' . $file_name . '.html')
                    );
                    $themes_file_data = array(
                        'platform' => $platform,
                        'filename' => $array['tmpl_path'],
                        'filetype' => 'html',
                        'fileuri'  => $array['theme'] . ':' . $array['tmpl_path'],
                        'version'  => $array['version'],
                        'theme'    => $array['theme'],
                        'memo'     => '模板文件',
                        'content'  => $array['content']
                    );
                    //fisrt, insert tmpl_file info to site_themes_file
                    //return the file's file_id_
                    if($file_id = $this->insert_themes_file($themes_file_data)){
                        $array['rel_file_id'] = $file_id;
                    }

                    $this->insert($array);
                    if(!$this->get_default($type, $theme)){
                        $this->set_default($type, $theme, $file_name.'.html');
                    }
                }
            }
        }
    }

    public function insert($data)
    {
        if(app::get('site')->model('themes_tmpl')->insert($data)){
            $this->set_all_tmpl_file($data['theme']);
            return true;
        }else{
            return false;
        }
    }

    public function insert_tmpl($data)
    {
        $dir = THEME_DIR . '/' . $data['theme'];
        if(!is_dir($dir))   return false;
        if(empty($data['tmpl_type']) || empty($data['content']))    return false;
        $data['tmpl_path'] = strtolower(preg_replace('/[^a-z0-9]/', '', $data['tmpl_path']));
        if($data['tmpl_path']){
            $target = $dir . '/' . $data['tmpl_type'] . '-' . $data['tmpl_path'] . '.html';
            if(is_file($target)){
                $target = '';
            }
        }
        if(empty($target)){
            $flag = true;
            for($i=1; $flag; $i++){
                $target = sprintf('%s/%s-(%s).html', $dir, $data['tmpl_type'], $i);
                if(file_exists($target))    continue;
                $flag = false;
            }
        }
        if(file_put_contents($target, $data['content'])){
            $data['tmpl_path'] = basename($target);
            $data['tmpl_name'] = ($data['tmpl_name']) ? $data['tmpl_name'] : basename($target);
            $data['version'] = filemtime($target);
            $themes_file_data = array(
                'filename' => $data['tmpl_path'],
                'filetype' => 'html',
                'fileuri'  => $data['theme'] . ':' . $data['tmpl_path'],
                'version'  => $data['version'],
                'theme'    => $data['theme'],
                'memo'     => '模板文件',
                'content'  => $data['content']
            );
            //先插入themes_file表，返回file_id
            if($file_id = $this->insert_themes_file($themes_file_data)){
                $data['rel_file_id'] = $file_id;
            }
            return $this->insert($data);
        }
        return false;
    }

    public function insert_themes_file($data){
        if($file_id = app::get('site')->model('themes_file')->insert($data)){
            return $file_id;
        }else{
            return false;
        }
    }

    public function copy_tmpl($tmpl, $theme)
    {
        $source = THEME_DIR . '/' . $theme . '/' . $tmpl;
        if(!is_file($source))   return false;
        $data = app::get('site')->model('themes_tmpl')->getList('*', array('theme'=>$theme, 'tmpl_path'=>$tmpl));
        $data = $data[0];
        if(empty($data))    return false;
        $flag = true;
        for($i=1; $flag; $i++){
            $target = sprintf('%s/%s/%s-(%s).html', THEME_DIR, $theme, $data['tmpl_type'], $i);
            if(file_exists($target))    continue;
            copy($source, $target);
            $flag = false;
        }
        unset($data['id']);
        $data['tmpl_path'] = basename($target);
        $data['tmpl_name'] = basename($target);
        if($this->insert($data)){
            $widgets = app::get('site')->model('widgets_instance')->getList('*', array('core_file'=>$theme.'/'.$tmpl));
            foreach($widgets AS $widget){
                unset($widget['widgets_id']);
                $widget['core_file'] = $theme . '/' . basename($target);
                $widget['modified'] = time();
                app::get('site')->model('widgets_instance')->insert($widget);
            }
            return true;
        }else{
            return false;
        }
    }

    public function delete_tmpl_by_theme()
    {
        //不删除实体文件，只处理数据库和conf
        $datas = app::get('site')->model('themes_tmpl')->getList('tmpl_path', array('theme'=>$theme));
        foreach($datas AS $data){
            $this->delete_tmpl($data['tmpl_path'], $theme);
        }
    }

    public function delete_tmpl($tmpl, $theme)
    {
        $source = THEME_DIR . '/' . $theme . '/' . $tmpl;
        if(!is_file($source))   return false;
        $data = app::get('site')->model('themes_tmpl')->getList('*', array('theme'=>$theme, 'tmpl_path'=>$tmpl));
        if($data[0]['id'] > 0){
            if(app::get('site')->model('themes_tmpl')->delete(array('id'=>$data[0]['id']))){
                //删除模板文件的同时删除themes_file的对应文件
                app::get('site')->model('themes_file')->delete(array('theme'=>$theme,'filename'=>$tmpl));
                app::get('site')->model('widgets_instance')->delete(array('core_file'=>$theme.'/'.$tmpl));
                $this->set_all_tmpl_file($data[0]['theme']);
                if($this->get_default($data[0]['tmpl_type'], $theme) == $data[0]['tmpl_path']){
                    $this->del_default($data[0]['tmpl_type'], $theme);
                }
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    private function __get_all_files($sDir, &$aFile, $loop=true){
        if($rHandle=opendir($sDir)){
            while(false!==($sItem=readdir($rHandle))){
                if ($sItem!='.' && $sItem!='..' && $sItem!='' && $sItem!='.svn' && $sItem!='_svn'){
                    if(is_dir($sDir.'/'.$sItem)){
                        if($loop){
                            $this->__get_all_files($sDir.'/'.$sItem,$aFile);
                        }
                    }else{
                        $aFile[]=$sDir.'/'.$sItem;
                    }
                }
            }
            closedir($rHandle);
        }
    }

    public function get_name($platform){
        $ctl = $this->__get_tmpl_list($platform);
        return $ctl;
    }

    public function get_list_name($platform, $name)
    {
        $name = rtrim(strtolower($name),'.html');
        $ctl = $this->__get_tmpl_list($platform);
        return $ctl[$name];
    }

    private function __get_tmpl_list($platform) {

        $ctl['pc'] = array(
            'index'=>app::get('site')->_('首页'),
            'topics'=>app::get('site')->_('一级类目页'),
            'shopcenter'=>app::get('site')->_('店铺首页'),
            'paycenter'=>app::get('site')->_('订单支付首页'),
            'gallery'=>app::get('site')->_('商品列表页'),
            'product'=>app::get('site')->_('商品详细页'),
            'cart'=>app::get('site')->_('购物车页'),
            'search'=>app::get('site')->_('高级搜索页'),
            'passport'=>app::get('site')->_('注册/登录页'),
            'member'=>app::get('site')->_('会员中心页'),
            'page'=>app::get('site')->_('站点栏目单独页'),
            'order_detail'=>app::get('site')->_('订单详细页'),
            'order_index'=>app::get('site')->_('订单确认页'),
            'default'=>app::get('site')->_('默认页'),
        );
        $ctl['wap'] = array(
            'index'=>app::get('site')->_('首页'),
            'topics'=>app::get('site')->_('一级类目页'),
            'shopcenter'=>app::get('site')->_('店铺首页'),
            'paycenter'=>app::get('site')->_('订单支付首页'),
            'gallery'=>app::get('site')->_('商品列表页'),
            'product'=>app::get('site')->_('商品详细页'),
            'cart'=>app::get('site')->_('购物车页'),
            'passport'=>app::get('site')->_('注册/登录页'),
            'member'=>app::get('site')->_('会员中心页'),
            'page'=>app::get('site')->_('站点栏目单独页'),
            'order_detail'=>app::get('site')->_('订单详细页'),
            'order_index'=>app::get('site')->_('订单确认页'),
            'default'=>app::get('site')->_('默认页'),
        );

        return $ctl[$platform];
    }


    public function touch_theme_tmpl($theme)
    {
        $db = app::get('site')->database();
        $qb = $db->createQueryBuilder();
        $rows = $qb->select('*')->from('site_themes_tmpl')->where('theme='.$qb->createPositionalParameter($theme))->execute()->fetchAll();
        if($rows){
            array_push($rows, array('tmpl_path'=>'block/header.html'), array('tmpl_path'=>'block/footer.html'));
            foreach($rows AS $row){
                $this->touch_tmpl_file($theme . '/' . $row['tmpl_path']);
            }
            kernel::single('site_theme_base')->set_theme_cache_version($theme);
        }

        $cache_keys = $db->executeQuery('SELECT `prefix`, `key` FROM base_kvstore WHERE `prefix` IN ("cache/template", "cache/theme")')->fetchAll();
        
        foreach($cache_keys as $value)
        {
            base_kvstore::instance($value['prefix'])->get_controller()->delete($value['key']);
        }

        app::get('base')->database()->executeUpdate('DELETE FROM base_kvstore WHERE `prefix` IN ("cache/template", "cache/theme")');

        //todo cacheobject touch tmpl时清缓存
        cacheobject::init(true);
        cacheobject::clean($msg);
        cacheobject::init(false);

        return true;
    }

    public function touch_tmpl_file($tmpl, $time=null)
    {
        if(empty($time))    $time = time();
        $source = THEME_DIR . '/' . $tmpl;
        if(is_file($source)){
            return @touch($source, $time);
        }else{
            return false;
        }
    }

    function output_pkg($theme){
        $tar = kernel::single('base_tar');
        $workdir = getcwd();

        if(chdir(THEME_DIR.'/'.$theme)){
            $this->__get_all_files('.',$aFile);
            for($i=0;$i<count($aFile);$i++){
                if($f = substr($aFile[$i],2)){
                    if($f!='theme.xml'){
                        $tar->addFile($f);
                    }
                }
            }
            if(is_file('info.xml')){
                $tar->addFile('info.xml',file_get_contents('info.xml'));
            }
            $tar->addFile('theme.xml',$this->make_configfile($theme));

            $aTheme = kernel::single('site_theme_base')->get_theme_info($theme);

            kernel::single('base_session')->close();

            $name = kernel::single('base_charset')->utf2local(preg_replace('/\s/','-',$aTheme['name'].'-'.$aTheme['version']),'zh');
            @set_time_limit(0);

            header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
            header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
            header('Content-type: application/octet-stream');
            header('Content-type: application/force-download');
            header('Content-Disposition: attachment; filename="'.$name.'.tgz"');
            $tar->getTar('output');
            chdir($workdir);
        }else{
            chdir($workdir);
            return false;
        }
    }

    public function make_configfile($theme)
    {
        $aTheme = kernel::single('site_theme_base')->get_theme_info($theme);

        $model = app::get('site')->model('widgets_instance');
        $qb = app::get('site')->database()->createQueryBuilder();
        $qb->select('*')
           ->from('site_widgets_instance')
           ->where($qb->expr()->like('core_file', $qb->createPositionalParameter($theme.'%')));

        $aWidget['widgets'] = $model->tidy_data($qb->execute()->fetchAll());
        foreach($aWidget['widgets'] as $i => &$widget){
            $widget['core_file'] = str_replace($theme.'/', '', $widget['core_file']);
            $widget['params'] = serialize($widget['params']);
        }
        $aTheme['id'] = $aTheme['theme'];
        $aTheme=array_merge($aTheme, $aWidget);

        return view::make('site/admin/theme/theme.xml', $aTheme);
    }

}//End Class
