<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class system_prism_data
{
    public function getBaseData()
    {
        $apis = config::get('apis.routes');
        $formatApi = array();
        foreach($apis as $key=>$api)
        {
            $apiGroup = $this->_genGroup($api);
            $apiMethod = $key;
            $routingKey = $api['uses'];
            //echo $key.':'.$routingKey."\n";continue;
            $apiParams = $this->_getParams($routingKey);
            $formatApi[$apiGroup][$apiMethod]['description'] = $apiParams['description'];
            $formatApi[$apiGroup][$apiMethod]['required_oauth'] = $api['oauth'];
            //$formatApi[$apiGroup][$apiMethod]['description'] = $this->_getDescription($routingkey);
            foreach($apiParams['params'] as $paramKey=>$param)
            {
                $formatApi[$apiGroup][$apiMethod]['params'][$paramKey]['name']        = $paramKey;
                //以后更新数据过滤，就采用这个方式获取必填项
                $formatApi[$apiGroup][$apiMethod]['params'][$paramKey]['required']    = $this->_genRequired($param['valid']);
                $formatApi[$apiGroup][$apiMethod]['params'][$paramKey]['type']        = $param['type'];
                $formatApi[$apiGroup][$apiMethod]['params'][$paramKey]['example']     = $param['example'];
                $formatApi[$apiGroup][$apiMethod]['params'][$paramKey]['default']     = $param['default'];
                $formatApi[$apiGroup][$apiMethod]['params'][$paramKey]['description'] = $param['description'];
            }
        }
        return $formatApi;
    }

    private function _genRequired($type)
    {
        if($this->_isInString($type, 'required'))
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    private function _isInString($string, $needle)
    {
        $string = '-_-!'.$string;
        return (bool)strpos($string, $needle);
    }

    private function _getDescription($routingkey)
    {
        $apiClass = $this->_getClass($routingKey);
        $description = $apiClass->apiDescription;
        return $description;
    }

    private function _getParams($routingKey) {
        $apiClass = $this->_getClass($routingKey);
      //if(function_exists($apiClass->getParams))
      //{
        $params = $apiClass->getParams();
        $params['description'] = $apiClass->apiDescription;
      //}
        return $params;
    }

    private function _getClass($routingKey)
    {
        $args = explode('@',$routingKey);
        $class = $args[0];
        if($class == '')
        {
            echo $routingKey;
            return null;
        }
        return new $class;
    }

    private function _genGroup($api)
    {
        $handlar = $api['uses'];
        $args = explode('_', $handlar);
        return $args[0];
    }
}

