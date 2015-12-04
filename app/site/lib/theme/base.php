<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class site_theme_base
{

    public function __construct()
    {
        $this->themesdir = array('wap'=>WAP_THEME_DIR, 'pc'=>THEME_DIR);
    }

    public function set_default($platform, $theme)
    {
        app::get('site')->model('themes')->update(array('is_used'=>0), array('platform'=>$platform));
        app::get('site')->model('themes')->update(array('is_used'=>1), array('theme'=>$theme));
        return app::get('site')->setConf($platform.'_current_theme', $theme);
    }

    public function get_default($platform='pc')
    {
        return app::get('site')->getConf($platform.'_current_theme');
    }//End Function

    public function update_theme($aData)
    {
        return app::get('site')->model('themes')->save($aData);
    }//End Function

    public function set_theme_cache_version($theme)
    {
        return app::get('site')->setConf('theme_cache_version.'.$theme, time());
    }//End Function

    public function get_theme_cache_version($theme)
    {
        return app::get('site')->getConf('theme_cache_version.'.$theme);
    }//End Function

    public function get_view($theme)
    {
        return ecos_cactus('site','theme_get_view',$theme);
    }//End Function


    public function get_basic_config($theme){
        $basic_config='on';
        $path = THEME_DIR.'/'.$theme;
        if(!is_dir($path))  return array();
        $workdir = getcwd();
        chdir($path);
        $xml = kernel::single('site_utility_xml');
        $content = file_get_contents('theme.xml');

        $config = $xml->xml2arrayValues($content);

        $basic_config = $config['theme']['basic_config']?$config['theme']['basic_config']['value']:$basic_config;
        chdir($workdir);
        return $basic_config;
    }

    public function get_theme_info($theme)
    {
        $qb = app::get('site')->database()->createQueryBuilder();
        return $qb->select('*')->from('site_themes')->where('theme='.$qb->createPositionalParameter($theme))->execute()->fetch();
    }//End Function

    public function install_theme_widgets($platform, $theme)
    {
        foreach(kernel::servicelist('site_theme_content_detector') AS $service){
            $service->update($platform, $theme);
            base_kvstore::instance('site_themes')->store('theme_last_modified'.get_class($service).$theme, $service->last_modified($theme));
        }
    }//End Function

    public function update_theme_widgets($platform, $theme, $force=true)
    {
        foreach(kernel::servicelist('site_theme_content_detector') AS $service){
            $last_modified = $service->last_modified($theme);
            if( $force
            || base_kvstore::instance('site_themes')->fetch('theme_last_modified'.get_class($service).$theme, $modified) === false
            || $last_modified != $modified ){
                logger::info('autofix theme widgets...');
                $service->update($platform, $theme);
                base_kvstore::instance('site_themes')->store('theme_last_modified'.get_class($service).$theme, $last_modified);
            }
        }
    }//End Function

    public function delete_theme_widgets($theme)
    {
        foreach(kernel::servicelist('site_theme_content_detector') AS $service){
            $service->clear_by_theme($theme);
            base_kvstore::instance('site_themes')->delete('theme_last_modified'.get_class($service).$theme);
        }
    }//End Function

    public function maintenance_theme_files($platform, $theme_dir=''){
        if (!$theme_dir) return;

        set_time_limit(0);
        cacheobject::init(false);
        header('Content-type: text/html;charset=utf-8');
        ignore_user_abort(false);
        ob_implicit_flush(1);
        ini_set('implicit_flush',true);
        kernel::$console_output = true;
        while(ob_get_level()){
            ob_end_flush();
        }
        echo str_repeat("\0",1024);
        echo '<pre>';
        echo '>update themes'."\n";

        if ($theme_dir==THEME_DIR){
            $dir = new DirectoryIterator($theme_dir);
            foreach($dir as $file)
            {
                $filename = $file->getFilename();
                if($filename{0}=='.'){
                    continue;
                }else{
                    $this->update_theme_widgets($platform, $filename);
                }
            }
        }
        else{
            $this->update_theme_widgets($platform, $theme_dir);
        }
        echo 'ok.</pre>';
    }

}//End Class
