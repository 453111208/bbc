<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */
class base_rpc_client
{
    public function __construct()
    {
    }

    public function call($method, $parameters = array(), $appId = 'default', $identity="")
    {
        if($identity)
        {
            switch($identity)
            {
            case "buyer":
                pamAccount::setAuthType('sysuser');
                $oauth['auth_type'] = pamAccount::getAuthType('sysuser');
                break;
            case "seller":
                pamAccount::setAuthType('sysshop');
                $oauth['auth_type'] = pamAccount::getAuthType('sysshop');
                break;
            case "shopadmin":
                pamAccount::setAuthType('desktop');
                $oauth['auth_type'] = pamAccount::getAuthType('desktop');
                break;
            }
            $oauth['account_id'] = pamAccount::getAccountId();
            $oauth['account_name'] = pamAccount::getLoginName();
        }
        $parameters['oauth'] = $oauth;


        if( $this->distribute() )
        {
            if($appId != 'default')
                $appId = $appId;
            return $this->callOutside($method, $parameters, $appId);
        }
        else
        {
            return $this->callInternal($method, $parameters);
        }
    }

    private function distribute()
    {
        if( config::get('prism.prismMode') )
        {
            return true;
        }
        return false;
    }

    protected function callInternal($flag, $parameters = array())
    {
        $apis = config::get('apis.routes');
        if (array_key_exists($flag, $apis))
        {
            list($class, $method) = explode('@', $apis[$flag]['uses']);
        }
        else
        {
            throw new InvalidArgumentException("Api [$flag] not defined");
        }

        $instance = new $class();

        $apiParams = $instance->getParams();
        //验证数据
        //通过传入数据和api原定义的类型进行比对
        apiUtil::paramsValidate($parameters, $apiParams);

        //因为有些数据需要批量处理，就防这里了
        $apiParameters = apiUtil::pretreatment($parameters, $apiParams);

        return call_user_func(array($instance, $method), $apiParameters);
    }

    protected function callOutside($method, $params, $appId)
    {
        $caller = new base_prism_caller;
        return $caller->call($method, $params, $appId);
    }
}

