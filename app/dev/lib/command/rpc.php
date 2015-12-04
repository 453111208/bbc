<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class dev_command_rpc extends base_shell_prototype{

    function command_call(){
        $args = func_get_args();
        $method = array_shift($args);
        $params = array();
        foreach($args as $set){
            if(preg_match('/^([a-z\_0-9]+)=(.+)$/',$set,$match)){
                $params[$match[1]] = $match[2];
            }
        }
        $var = app::get('base')->remote(1)->call($method,$params);
    }

    var $command_depends_info = '执行测试用例';
    function command_depends_info()
    {
        $dir = __DIR__;
        $appDir = rtrim($dir, 'dev/lib/command');
        $searchKey = "rpcCall(";
        $command = 'find ' . $appDir . ' -type f | xargs grep "' . $searchKey . '"';
        exec($command, $output, $res1);

        $apiInfos = config::get('apis.routes');

        $apiDependsInfo = array();
        foreach ($output as $code)
        {
            $code = substr($code, strlen($appDir));
            $code = ltrim($code, '/');

            //获取接口调用的app，是由哪个接口调用的
            $appName = explode('/',$code);
            //这里把testcase里面的东西过滤掉了，
            //没办法，这个里面的代码太挫了，不能作为参考数据去考虑
            if($appName[1] == 'testcase')
                continue;
            $appName = $appName[0];

            //这里要获取调用的是哪个接口了。
            $apiRoutingKey = explode('rpcCall(', $code);
            $apiRoutingKey = explode("'", $apiRoutingKey[1]);
            $apiRoutingKey = $apiRoutingKey[1];

            //把apiRoutingKey转化成这个api所属于的app
            $apiInfo = $apiInfos[$apiRoutingKey];
            if( is_null($apiInfo) || $apiInfo == null )
            {
                continue;
            }
            else
            {
                $handler = $apiInfo['uses'];
                $dependsAppName = explode('_', $handler);
                $dependsAppName = $dependsAppName[0];
            }

            $apiDependsInfo[$appName][$dependsAppName] = [
                    'appName' => $dependsAppName,
                    'path' => '*',
                    'limit_count' => 1000,
                    'limit_seconds' => 60,
                ];
//            var_dump($dependsAppName);
//            var_dump($code);
//            var_dump($apiRoutingKey);
        }
        var_export($apiDependsInfo);
        //var_dump($output);
    }
}
