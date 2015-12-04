<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class system_prism_init_bind extends system_prism_init_base
{

    public function bind($apiName)
    {
        $conn = system_prism_init_util::getAdminConn();

        $bindInfo = config::get('apis.depends');
        foreach($bindInfo as $appName=>$apiList)
        {
            foreach($apiList as $apiName=>$apiInfo)
            {
                $apiName = $apiInfo['appName'];
                $params = array(
                    'app_id' => system_prism_init_util::getAppId($appName),
                    'api_id' => system_prism_init_util::getApiId($apiName),
                    'path'   => $apiInfo['path'],
                    'limit_count' => $apiInfo['limit_count'],
                    'limit_seconds' => $apiInfo['limit_seconds'],
                );
                $this->call($conn, '/api/platform/manageapp/bind', $params, 'post');
            }
        }

        return true;
    }

}

