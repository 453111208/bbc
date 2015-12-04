<?php
class syslogistics_api_getHqepayTracking{
    public $apiDescription = "获取华强宝物流跟踪";
    public function getParams()
    {
        $return['params'] = array(
            'logi_no' =>['type'=>'string','valid'=>'required', 'description'=>'运单号','default'=>'','example'=>'1'],
            'corp_code' =>['type'=>'string','valid'=>'required', 'description'=>'物流公司编码','default'=>'','example'=>'SF'],
        );
        return $return;
    }
    public function getTracking($params)
    {
        $tracker = kernel::single('syslogistics_data_tracker')->pullFromHqepay($params['logi_no'],$params['corp_code']);
        return $tracker;
    }
}
