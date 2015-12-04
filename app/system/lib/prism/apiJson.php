<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 * @author guocheng
 */

class system_prism_apiJson
{
    public function getJsonUrl()
    {
        $apiJson = $this->format();
        foreach($apiJson as $key=>$value)
        {
            $url[$key] = url::route('api/api.json', ['api'=>$key]) ;
        }
        return $url;
    }

    public function getJson()
    {
        return $this->format();
    }

    public function storeJson()
    {
        $apiJson = $this->format();
        return $apiJson;
    }

    public function genApisDesc($apisKey)
    {
        $apiNames = config::get('prism.prismApiName');
        return $apiNames[$apisKey] ? $apiNames[$apisKey] : $apisKey;
    }

    public function format()
    {
        $apiInfo = kernel::single('system_prism_data')->getBaseData();
        $apiInfoJSONs = array();
        foreach($apiInfo as $apisKey=>$apis)
        {
            $apiInfoJSON = array();
            $apiInfoJSON['url'] = kernel::base_url(1).'/index.php/api';
//          $apiInfoJSON['interface'] = '';
//          $apiInfoJSON['resource_content_types'] = null;
            $apiInfoJSON['prefix'] = $apisKey;
            $apiInfoJSON['summary'] = $this->genApisDesc($apisKey)."相关";
            foreach ($apis as $apiKey=>$api)
            {
//              $apiInfoJSON['apis'][$apiKey]['path'] = '';
                $apiInfoJSON['apis'][$apiKey]['method'] = ['GET','POST'];
                $apiInfoJSON['apis'][$apiKey]['summary'] = $api['description'];
//              $apiInfoJSON['apis'][$apiKey]['notes'] = '';
                $apiInfoJSON['apis'][$apiKey]['require_oauth'] = $api['required_oauth'];
                $apiInfoJSON['apis'][$apiKey]['backend_timeout_second'] = 0;
                $apiInfoJSON['apis'][$apiKey]['params'] = $this->getSystemParams();
                foreach($api['params'] as $param)
                {
                    $tmpParam = array();
                    $tmpParam['name'] = $param['name'];
                    $tmpParam['desc'] = $param['description'];
                    $tmpParam['required'] = $param['required'];
                    $tmpParam['type'] = $param['type'];
                    $tmpParam['param_type'] = 'param';
                    $apiInfoJSON['apis'][$apiKey]['params'][] = $tmpParam;
                    unset($tmpParam);
                }
//              $apiInfoJSON['apis'][$apiKey]['response'] = '';
                $apiInfoJSON['apis'][$apiKey]['exception'] = [
                    [
                        'http_code'=>200,
//                      'code'=>'',
//                      'message'=>''
                    ]
                ];
            }
            $apiInfoJSON['mode'] = 'param';
            $apiInfoJSON['param_name'] = 'method';
//          $apiInfoJSON['auto_config_url'] = '';
            $apiInfoJSON['config_values'] = [
                    [
                        'name'=>'token',
                        'description'=>'站点的节点token',
//                      'default_value'=>'',
                        'is_secret'=>true,
                    ]
                ];
            $apiInfoJSON['global_params'] = [
                    [
                        'name'=>'sign_type',
                        'param_type'=>'REQUEST',
                        'value_type'=>'expr',
                        'format'=>'MD5',
                    ],
                    [
                        'name'=>'timestamp',
                        'param_type'=>'REQUEST',
                        'value_type'=>'datetime',
                        'format'=>'timestamp',
                    ],
                    [
                        'name'=>'sign',
                        'param_type'=>'REQUEST',
                        'value_type'=>'sign',
                        'format'=>'ecos',
                    ]
                ];
//          $apiInfoJSON['notify_publish']='';
//          $apiInfoJSON['notify_receive']='';

            $apiInfoJSONs[$apisKey] = json_encode($apiInfoJSON);
            unset($apiInfoJSON);
        }
        return $apiInfoJSONs;
    }


    public function removeNull(&$params)
    {
        foreach ($params as $key => $param)
        {
            if($param == null || $param == "")
            {
                unset($params[$key]);
            }
            elseif(is_array($param))
            {
                $this->removeNull($params[$key]);
            }
        }
    }

    public function getSystemParams()
    {
        return [
                [
                    'name'=>'format',
                    'desc'=>'返回格式：目前仅支持json和xml',
                    'required'=>true,
                    'type'=>'string',
                    'param_type'=>'param'
                ],
                [
                    'name'=>'v',
                    'desc'=>'版本号',
                    'required'=>true,
                    'type'=>'string',
                    'param_type'=>'param'
                ],
            ];
    }
}

