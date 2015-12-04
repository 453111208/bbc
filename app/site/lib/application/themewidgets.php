<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
 

class site_application_themewidgets extends site_application_prototype_themefile
{
    var $path = 'widgets';

    public function install($platform) 
    {
        if(is_dir($this->getPathname())){
            $widgets_name = basename($this->getPathname());
            $theme = $this->target_theme;
            logger::info('Installing Theme Widgets '. $theme . ':' . $widgets_name);
            $data['theme'] = $theme;
            $data['name'] = $widgets_name;
            $data['platform'] = $platform;
            app::get('site')->model('widgets')->insert($data);
        }
    }//End Function
    
    function clear_by_theme($theme){
        if(empty($theme)){
            return false;
        }
        app::get('site')->model('widgets')->delete(array(
            'theme'=>$theme));
    }
    
    function update($platform, $theme){
        $this->clear_by_theme($theme);
        foreach($this->detect($theme) as $name=>$item){
            $item->install($platform);
        }
        return true;
    }
}//End Class
