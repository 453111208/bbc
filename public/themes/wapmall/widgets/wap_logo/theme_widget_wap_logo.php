<?php

function theme_widget_wap_logo($setting)
{
    $logo_id = app::get('sysconf')->getConf('sysconf_setting.wap_logo');
    $result['logo_image'] = $logo_id;

    return $result;
}
?>
