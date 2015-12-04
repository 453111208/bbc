<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class sysopen_prism extends system_prism_init_base
{

    public function create($type)
    {
        return $this->__createKeyForOpen($type);
    }

    public function delete($key)
    {
        return $this->__deleteKeyForOpen($key);
    }

    private function __createKeyForOpen($type)
    {
        $conn = system_prism_init_util::getUserConn();

        $params = [
                'app_id' => system_prism_init_util::getAppId($type),
            ];
        $result = $this->call($conn, '/api/platform/key/create', $params, 'post' );

        return $result;
    }

    private function __deleteKeyForOpen($key)
    {
        $conn = system_prism_init_util::getUserConn();

        $params = [
                'key' => $key,
            ];
        $result = $this->call($conn, '/api/platform/key/delete', $params, 'post' );

        return true;
    }


}
