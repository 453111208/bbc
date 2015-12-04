<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_prism_caller
{

    public function call($method, $params, $appId)
    {
        $prismHost = config::get('prism.prismHostUrl');
        $prismSocketFile = config::get('prism.prismSocketFile');
        //获取应用对应的key和secret
        $keySecret = apiUtil::getPrismKey($appId);
        $key       = $keySecret['key'];
        $secret    = $keySecret['secret'];

        //获取path
        $path = apiUtil::genApiPath($method);

        //params加入系统数据
        $systemParams = $this->__genSystemParams($method);
        $params = array_merge($params, $systemParams);
        $params['method'] = $method;

        //实例化请求工具
        $client = new base_prism_client($prismHost, $key, $secret, $prismSocketFile);
        //$path = "/api/bbc?method=bbc.test";


        $result = $client->post($path, $params);
        //日志记录
        //之前request和response分开记录的，发现不容易找，只好放一起了
        logger::info('call API : ' . $method . "\n"
            . 'wiht host : '. $prismHost  . "\n"
            . 'wiht key : '. $key  . "\n"
            . 'wiht secret : '. $secret  . "\n"
            . 'with params :' . var_export($params, 1) . "\n"
            . 'api result : ' . $result . "\n"
        );

        //这里是返回数据
        $result = json_decode($result, 1);
        if( $result['error'] == null )
        {
            return $result['result'];
        }
        else
        {
            //根据返回的数据是否错误，如果有错误，尽量以原有异常抛出
            $exception = $result['error']['exception'] ? $result['error']['exception'] : 'Exception';
            logger::error(var_export($result, 1));
            $e = new $exception($result['error']['message']);
            throw $e;
        }
    }

    //params加入系统数据
    private function __genSystemParams($method)
    {
        $apiInfoes = config::get('apis.routes');
        $apiInfo   = $apiInfoes[$method];
        $version = $apiInfo['version'][0];
        $systemParams = [
                'format'    => 'json',
                'v'         => $version,
            ];
        return $systemParams;
    }
}

