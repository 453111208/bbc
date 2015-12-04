<?php
class dev_docbuilder_ad
{
    public $doc_title = "ONEX B2B2C 商城API文档";

    public function getApiDoc($api_key, $doc_type = 'md'){


        $apis = config::get('apis.routes');

        if($api_key == null || $apis[$api_key] == null)
        {
            throw new LogicException('not found the API with the name :' . $api_key);
        }

        $api_conf = $apis[$api_key];
        $handle = $api_conf['uses'];
        list($class, $method) = explode('@', $handle);
        $handlar = new $class;

        //这里方法名和那个出逻辑问题了，以后这里要重写
        $doc['title1'] = $this->__getTitle1($handlar, $api_key);
        $doc['title2'] = $this->__getTitle2($handle);
        $doc['version'] = $this->__getVersion($api_conf);
        $doc['api_conf'] = $api_conf;
        $doc['request_title'] = $this->__getRequestTitle();
        $doc['system_params'] = $this->__getSystemParams();
        $doc['params'] = $this->__getParams($handlar);
        $doc['response'] = $this->__getResponse($handlar);
        return $doc;

      //$docer = kernel::single('dev_docbuilder_ad_md');

      //return $docer->gendoc($doc);
    }

    private function __getTitle2($handle)
    {
        $args = explode('_', $handle);
        $app_name = $args[0];
        $app_titles = config::get('prism.prismApiName', array());
        $app_title = $app_titles[$app_name] ? $app_titles[$app_name] . '相关API' : $app_name;

        return $app_title;
    }

    private function __getTitle1($api_handlar, $api_key)
    {
        return $api_handlar->apiDescription . '(' . $api_key . ')';
    }

    private function __getRequestTitle()
    {
        return [
            'field' => '字段',
            'title' => '标题',
            'type' => '数据类型',
            'validate' => '验证条件',
            'example' => '示例值',
            'default' => '默认值',
            'description' => '详细说明',
            ];
    }

    private function __getVersion($api_conf)
    {
        return $api_conf['version'];
    }

    private function __getSystemParams()
    {
        return [
            [
            'field' => 'method',
            'title' => 'API的方法名',
            'type' => 'string',
            'validate' => 'required',
            'example' => 'trade.get',
            'default' => 'null',
            'description' => '标识请求的是哪个API',
             ],
          //[
          //'field' => 'timestamp',
          //'title' => '请求时间',
          //'type' => 'unix时间戳',
          //'validate' => 'required | numeric | > time()-300',
          //'example' => '1440596821',
          //'default' => 'null',
          //'description' => '标识API请求的发起时间，如果超时300秒则拒绝请求',
          // ],
            [
            'field' => 'format',
            'title' => '返回数据格式',
            'type' => 'string',
            'validate' => 'required',
            'example' => 'json',
            'default' => 'json',
            'description' => '返回数据是json格式的，目前只支持json',
             ],
             [
            'field' => 'v',
            'title' => '版本号',
            'type' => 'string',
            'validate' => 'required',
            'example' => 'v1',
            'default' => 'null',
            'description' => '标识该接口的版本',
             ],
          // [
          //'field' => 'sign_type',
          //'title' => '签名方式',
          //'type' => 'string',
          //'validate' => 'required',
          //'example' => 'MD5',
          //'default' => 'null',
          //'description' => '标识签名算法',
          // ],
          // [
          //'field' => 'sign',
          //'title' => '签名',
          //'type' => 'string',
          //'validate' => 'required',
          //'example' => '',
          //'default' => 'null',
          //'description' => '数据签名，32位长度16进制数字',
          // ],
            ];
    }

    private function __getParams($handlar)
    {
        $return = $handlar->getParams();
        $params = $return['params'];
        $ret = array();
        foreach($params as $key=>$value)
        {
            $field = array();
            $field['field'] = $key;
            $field['title'] = $value['name'];
            $field['type'] = $value['type'];
            $field['validate'] = $value['valid'];
            $field['example'] = $value['example'];
            $field['default'] = $value['default'];
            $field['description'] = $value['description'];
            $ret[] = $field;
        }
        return $ret;
    }

    private function __getResponse($handlar)
    {
        $return = $handlar->getParams();
        $response = $return['response'];
        return $response;
    }


}
