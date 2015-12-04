<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class system_task{

    function post_update($params){
        $dbver = $params['dbver'];
        if(version_compare($dbver,'0.4','<')){
            if (system_queue::write_config()){
                logger::info('Writing queue config file ... ok.');
            }else{
                trigger_error('Writing queue config file fail, Please check config directory has write permission.', E_USER_ERROR);
            }
        }

        if(version_compare($dbver,'0.5','<')){
            $storeKeys = ['prismInitComplete','prismApiInfo', 'prismApiReady', 'prismAppInfo', 'prismUserKey', 'prismUserSecret', 'prismUserInfo', 'prismApiLog', 'prismApiReady'];
            foreach($storeKeys as $key)
            {
                $data = app::get('system')->getConf($key);
                if( $data != null )
                {
                    kernel::single('system_prism_store')->set($key, $data);
                    app::get('system')->setConf($key, null);
                    logger::info('clean prism setting');
                }
            }
        }
    }
}
