<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class system_prism_init_app extends system_prism_init_base
{
    public function create($appName)
    {
        $conn = system_prism_init_util::getUserConn();
        $params = [
                'name' => config::get('prism.prismAppName') . $appName,
                'desc' => '该用户为CommerceBBC专用用户，请勿随意修改'
            ];
        $result = $this->call($conn, '/api/platform/app/create', $params, 'post');
        $appInfo = kernel::single('system_prism_store')->get('prismAppInfo');
        $appInfo[$appName] = $result;
        kernel::single('system_prism_store')->set('prismAppInfo', $appInfo);
        return true;
    }

    public function createKey($appName)
    {
        $conn = system_prism_init_util::getUserConn();
        $params = [
                'app_id' => system_prism_init_util::getAppId($appName),
            ];
        $result = $this->call($conn, '/api/platform/key/create', $params, 'post' );
        $appKeys = app::get('base')->getConf('prismKeys');
        $appKeys[$appName] = $result;
        app::get('base')->setConf('prismKeys', $appKeys);

        return $result;
    }

    public function info($appName)
    {
        $conn = system_prism_init_util::getUserConn();
        $params = [
            'app_id' => system_prism_init_util::getAppId($appName),
        ];
        $result = $this->call($conn, '/api/platform/app/info/' . $params['app_id'], $params, 'get');
        return $result;
    }

    public function setQueueConsume($appName)
    {
        $conn = system_prism_init_util::getAdminConn();
        $params = [
            'app_id' => system_prism_init_util::getAppId($appName),
        ];

    }

    public function setQeueuePublish($appName)
    {


    }

}
