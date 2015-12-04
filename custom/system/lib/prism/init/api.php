<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class system_prism_init_api extends system_prism_init_base
{
    public function import($apiName)
    {
        $conn = system_prism_init_util::getUserConn();
        $apiJson = kernel::single('system_prism_apiJson')->getJsonUrl();
        $params = [
                'app_id' => system_prism_init_util::getAppId($apiName),
                'url' => $apiJson[$apiName],
            ];
        $result = $this->call($conn, '/api/platform/service/import', $params, 'post' );
        $apiInfos = kernel::single('system_prism_store')->get('prismApiInfo');
        $apiInfos[$apiName] = $result;
        kernel::single('system_prism_store')->set('prismApiInfo', $apiInfos);
        return $result;
    }

    public function refresh($apiName)
    {
        $conn = system_prism_init_util::getUserConn();
        $apiJson = kernel::single('system_prism_apiJson')->getJsonUrl();
        $params = [
                'app_id' => system_prism_init_util::getAppId($appName),
                'url' => $apiJson[$apiName],
            ];
        $result = $this->call($conn, '/api/platform/service/refresh', $params, 'post' );
        return $result;
    }

    public function updateUrl($apiName)
    {
        $newApiJsonUrls = kernel::single('system_prism_apiJson')->getJsonUrl();
        $oldApiJsonUrls = kernel::single('system_prism_store')->get('prismApiReady');
        $newApiJsonUrl = $newApiJsonUrls[$apiName];
        $oldApiJsonUrl = $oldApiJsonUrls[$apiName];
        $conn = system_prism_init_util::getAdminConn();
        $params = [
                'origin' => $oldApiJsonUrl,
                'url'    => $newApiJsonUrl,
            ];
        $result = $this->call($conn, '/api/platform/manageapi/modify/remoteurl', $params, 'post' );
        return $result;
    }

    public function setConf($apiName)
    {
        $conn = system_prism_init_util::getUserConn();
        $apiId = system_prism_init_util::getApiId($apiName);
        $params = [
                'Id' => $apiId,
                'key' => 'token',
                'value' => base_shopnode::token()
            ];
        $this->call($conn, '/api/platform/service/config/set', $params, 'post' );
        return true;

    }

    public function online($apiName)
    {
        $conn = system_prism_init_util::getAdminConn();
        $apiId = system_prism_init_util::getApiId($apiName);
        $params = [
            'Id' => $apiId,
            ];
        $this->call($conn, '/api/platform/manageapi/online', $params, 'post');
        return true;
    }

}

