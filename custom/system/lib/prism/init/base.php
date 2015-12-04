<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class system_prism_init_base
{

    public function call($conn, $path, $params, $method_type='get')
    {
        $caller = new PrismClient($conn['host'], $conn['key'], $conn['secret']);
        if( in_array($method_type, array('get', 'post', 'delete', 'put')) )
            $result = call_user_func_array(array($caller, $method_type), array($path, $params));
        $result = json_decode($result, true);
        if( $result['error'] != null )
        {
            logger::error(var_export([
                'conn' => $conn,
                'path' => $path,
                'params' => $params,
                'method' => $method_type,
                'result' => $result
                ],1));
            throw new RuntimeException( $result['error']['code'] . ':' . $result['error']['message'], $result['error']['code'] );
        }
        return $result['result'];
    }
}

