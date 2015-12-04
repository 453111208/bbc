<?php
function theme_widget_ad_fourpic(&$setting) {
    $setting['allimg']="";
    $setting['allurl']="";
    if($system->theme){
        $theme_dir = kernel::get_themes_host_url().'/'.theme::getThemeName();
    }else{
        $theme_dir = kernel::get_themes_host_url().'/'.app::get('site')->getConf('current_theme');
    }
    if(!$setting['pic']){
        foreach($setting['img'] as $value){
            $setting['allimg'].=$rvalue."|";
            $setting['allurl'].=urlencode($value["url"])."|";
        }
    }else{
        foreach($setting['pic'] as $key=>$value){
            if($value['link']){
                if($value["url"]){
                    $value["linktarget"]=$value["url"];
                }
                $setting['allimg'].=$rvalue."|";
                $setting['allurl'].=urlencode($value["linktarget"])."|";
                $setting['pic'][$key]['link'] = str_replace('%THEME%',$theme_dir,$value['link']);
            }
        }
    }
    return $setting;

}

?>
