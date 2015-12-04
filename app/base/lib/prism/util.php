<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2012 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class base_prism_util
{

    static public function getAppKey($appName)
    {
        $appKey = $appName;
        return $appKey;
    }

    //这里获取key和secret
    static public function getPrismKey($appId = 'default')
    {
        $keys = app::get('base')->getConf('prismKeys');
        return $keys[$appId];
    }

    //这里是根据prism的path以及json数据生成的API的PATH，这里是统一入口点，如果有一天path要修改了，这里也要修改
    static public function genApiPath($method)
    {

        $apiInfoes = config::get('apis.routes');
        $apiInfo   = $apiInfoes[$method];
        $handlar   = $apiInfo['uses'];
        $method_arr = explode('_',$handlar);

      //$method_arr = explode('.',$method);
      //$apiDomain = $method_arr[0];
        $path = '/api/' . $method_arr[0] . '?method=' . $method;
        return $path;
    }

    static public function pretreatment($parameters, $apiParams)
    {
        foreach($apiParams['params'] as $field=>$value )
        {
            //返回值约束字段
            if( $value['type'] == 'field_list' ) $fieldList = $field;
        }

        //处理需要返回的字段约束
        if( isset($parameters[$fieldList]) && $apiParams['extendsFields'] )
        {
            $extendsFields = $apiParams['extendsFields'];
            $parameters[$fieldList] = format_fields($parameters[$fieldList], $extendsFields);
        }
        return $parameters;
    }

    static public function paramsValidate($params, $paramsInfos)
    {
        if( is_array($paramsInfos) )
        {
            //获取参数设定，组织成一validator的要求格式
            $paramsValidate = [];
            $paramsName     = [];
            foreach( $paramsInfos['params'] as $paramKey => $paramsInfo )
            {
                $paramsValidate[$paramKey] = $paramsInfo['valid'];
                //$paramsName[$paramKey] = $paramsInfo['description'];
            }

            //这里验证数据
            $validator = validator::make($params, $paramsValidate);
            if( $validator->fails() )
            {
                $errors = json_decode( $validator->messages(), 1 );
                foreach( $errors as $error )
                {
                    throw new LogicException( $error[0] );
                }
            }
        }
        else
        {
            throw new LogicException('params设置异常');
        }
    }
}

