<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class system_prism_init_user extends system_prism_init_base
{
    public function create()
    {
        $conn = system_prism_init_util::getAdminConn();
        $params = [
                'email' => config::get('prism.prismUserEmail'),
                'password' => config::get('prism.prismUserPassword'),
                'summary' => '该用户为CommerceBBC专用用户，请勿随意修改',
            ];

        $this->call( $conn, '/api/platform/manageuser/create', $params, 'post' );
        return true;
    }

    public function active()
    {
        $conn = system_prism_init_util::getAdminConn();
        $params = [
                'email' => config::get('prism.prismUserEmail'),
            ];
        $this->call( $conn, '/api/platform/manageuser/active', $params, 'post' );
        return true;
    }

    public function apiprovider()
    {
        $conn = system_prism_init_util::getAdminConn();
        $params = [
                'email' => config::get('prism.prismUserEmail'),
            ];
        $this->call( $conn, '/api/platform/manageuser/apiprovider', $params, 'post' );
        return true;
    }

    public function info()
    {
        $conn = system_prism_init_util::getAdminConn();
        $params = [
                'email' => config::get('prism.prismUserEmail'),
            ];
        $result = $this->call( $conn, '/api/platform/manageuser/info/'.$params['email'], $params, 'get' );

        $key = $result['Key'];
        $secret = $result['Secret'];

        kernel::single('system_prism_store')->set('prismUserKey', $key);
        kernel::single('system_prism_store')->set('prismUserSecret', $secret);
        kernel::single('system_prism_store')->set('prismUserInfo', $result);

        return $result;
    }
}

