<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class system_prism_init_oauth extends system_prism_init_base
{

    public function set()
    {
        $conn = system_prism_init_util::getAdminConn();

        $appName = 'sysopen';
        $keySecret = apiUtil::getPrismKey($appName);

        $url = config::get('prism.prismHostUrl') . apiUtil::genApiPath('open.oauth.login') . '&v=v1&format=json';

        $config = array(
            'CfgIdColumn' => 'accountid',
            'Host' => $url,
            'Key' => $keySecret['key'],
            'Secret' => $keySecret['secret'],
            "Meta" => "{\"type\":\"oauth_type\"}",
            "Response" => "result",
        );

        $params = array('is_sandbox'=>false, 'config'=>json_encode($config));
        logger::info('oauth on prism set : ' . var_export($params, true));

        $this->call( $conn, '/api/platform/manageoauth/config/set', $params, 'post' );

        return true;
    }

    public function get()
    {
        $conn = system_prism_init_util::getAdminConn();
        return $this->call( $conn, '/api/platform/manageoauth/config/get', $params, 'post' );
    }

}

