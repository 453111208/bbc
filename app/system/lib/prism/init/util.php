<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class system_prism_init_util
{
    public static function getAdminConn()
    {
        $host   = config::get('prism.prismHostUrl');
        $key    = config::get('prism.prismAdminKey');
        $secret = config::get('prism.prismAdminSecret');

        return [
                'host'   => $host,
                'key'    => $key,
                'secret' => $secret
            ];
    }

    public static function getUserConn()
    {
        $host   = config::get('prism.prismHostUrl');
        $key    = kernel::single('system_prism_store')->get('prismUserKey');
        $secret = kernel::single('system_prism_store')->get('prismUserSecret');

        return [
                'host'   => $host,
                'key'    => $key,
                'secret' => $secret
            ];
    }

    public static function getAppConn($appName)
    {
        $host   = config::get('prism.prismHostUrl');
        $keySecret = apiUtil::getPrismKey($appName);
        $key       = $keySecret['key'];
        $secret    = $keySecret['secret'];

        return [
                'host'   => $host,
                'key'    => $key,
                'secret' => $secret
            ];
    }

    public static function getAppId($appName)
    {
        $appInfo = kernel::single('system_prism_store')->get('prismAppInfo');
//        print_r($appInfo);exit;
        return $appInfo[$appName]['Id'];
    }

    public static function getApiId($apiName)
    {
        $apiInfo = kernel::single('system_prism_store')->get('prismApiInfo');
        return $apiInfo[$apiName]['id'];
    }

}
