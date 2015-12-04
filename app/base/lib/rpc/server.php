<?php

class base_rpc_server
{

    public function process($request, $response)
    {
        if($request == null)
            $request  = new base_prism_request;
        if($response == null)
            $response = new base_prism_response;
        $params = $request->getParams();

        //过滤systemParams，并返回api的配置
        //对systemParams进行判断，没有进行剥离，在下面的__unsetSystemParams方法中剥离的。
        $api = $this->__getApiInfo($params, $response);

        //实例化api class
        $handler = $api['uses'];
        list($class_name, $action_name) = explode('@', $handler);
        $class = new $class_name;



        $oauth = $request->getOauth();
        $appInfo = $request->getAppInfo();


        //这里做个日志点
        //这个日志点埋在系统参数处理之后，剥离系统参数之前
        //理由是系统参数验证正确后，该请求来自prism，当被外界请求会被拦截下来
        logger::info(
            'request_id : '         . $request->getRequestID()                   . "\n" .
            'request_ip : '         . $request->getCallerIP()                    . "\n" .
            'request_app_info : '   . var_export($appInfo, true)   . "\n" .
            'request_oauth_info : ' . var_export($oauth, true)     . "\n" .
            'request_params : '     . var_export($params, true)
        );

        //从params里面剥离systemParams
        $format = $params['format'];
        $this->__unsetSystemParams($params);

        //验证方法和参数
        try
        {
            $params = $this->checkParams($class, $action_name, $params);
            $result = call_user_func([$class, $action_name], $params, $oauth, $appInfo);

            //这里记录返回的日志，原本想放在response里面，考虑了一下那里不好获取request_id，就放这里了
            logger::info(
                'request_id : ' . $request->getRequestID() . "\n" .
                'reponse_info : ' . var_export($result, true)
            );
            return $response->send($result, $format);
        }
        catch(Exception $e)
        {
            $exceptionClass = get_class($e);
            $errorMessage = $e->getMessage();
            logger::error(
                'request_id : ' . $request->getRequestID() . "\n" .
                'message : ' . $e->getMessage() . "\n" .
                'string : ' . $e->__toString()
            );
            return $response->sendError($method.".runtimeException", $errorMessage, $format, $exceptionClass);
        }
    }

    public function checkParams(&$class, &$action_name, &$params)
    {
        if( !method_exists( $class, 'getParams' ) )
        {
            throw new RuntimeException('获取参数列表失败');
        }
        $paramsInfos = $class->getParams();
        apiUtil::paramsValidate($params, $paramsInfos);

        //预处理下params
        //转化下数据的格式，比如对数据结构fields进行转化和extends扩展等
        $params = apiUtil::pretreatment($params, $paramsInfos);

        //判断下方法是否存在
        if( !method_exists( $class, $action_name ) )
        {
            throw new RuntimeException('找不到方法 :' . $action_name);
        }
        return $params;
    }

    private function __getApiInfo(&$params, &$response)
    {
        //提取系统参数
        $method    = $params['method'];
        $timestamp = $params['timestamp'];
        $format    = $params['format'];
        $v         = $params['v'];
        $sign_type = $params['sign_type'];
        $sign      = $params['sign'];
        $exception = 'LogicException';

        if( !base_rpc_validate::isValidate($params) )
        {
            return $response->sendError('system.systemParams.signError', app::get('base')->_('签名错误'), $format, $exception);
        }

        if( !( $format == 'json' || $format == 'xml' ) )
        {
            return $response->sendError('system.systemParams.formatError', app::get('base')->_('返回格式设定必须是json或者xml'), $format, $exception);
        }

        if( !is_numeric($timestamp) )
        {
            return $response->sendError('system.systemParams.timestampFormatError', app::get('base')->_('时间格式错误（包含非数字的字符）'), $format, $exception);
        }

        if( time() - intval($timestamp) > 300 )
        {
            return $response->sendError('system.systemParams.timeOut', app::get('base')->_('请求已超时'), $format, $exception);
        }

        $apis = config::get('apis.routes');
        if( !isset($apis[$method]) )
        {
            return $response->sendError('system.systemParams.methodNotFound', app::get('base')->_('找不到请求API'), $format, $exception);
        }

        if( !in_array($v, $apis[$method]['version']) )
        {
            return $response->sendError('system.systemParams.versionNotMatch', app::get('base')->_('API版本不匹配'), $format, $exception);
        }

        return $apis[$method];
    }

    private function __unsetSystemParams(&$params)
    {
        $systemParamKeys = ['method', 'timestamp', 'format', 'v', 'sign_type', 'sign'];
        foreach($systemParamKeys as $key)
        {
            unset($params[$key]);
        }
    }

}
