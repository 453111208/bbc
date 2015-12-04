<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_syscache_setting extends base_syscache_abstract implements base_interface_syscache_farmer {
    
    public function get_data(){
        $pri_settings = app::get('base')->database()->executeQuery('select app, `key`, value from base_setting')->fetchAll();
        $settings = array();

        foreach( $pri_settings as $setting ){
            $settings['setting/'.$setting['app'].'-'.$setting['key']] = unserialize($setting['value']);
        }

        return $settings;
    }
}

