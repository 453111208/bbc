<?php
class syslogistics_api_fare{
    public $apiDescription = "计算运费";
    public function getParams()
    {
        $return['params'] = array(
            'template_id' =>['type'=>'string','valid'=>'required', 'description'=>'模板id','default'=>'','example'=>'1'],
            'weight' =>['type'=>'string','valid'=>'', 'description'=>'商品重量(单位：kg)','default'=>'','example'=>'10'],
            'areaIds' =>['type'=>'string','valid'=>'required', 'description'=>'收货地区的代号集合','default'=>'','example'=>'1'],
        );
        return $return;
    }
    public function countFare($params)
    {
        $templateId = $params['template_id'];
        $weight = $params['weight'];
        $areaIds = $params['areaIds'];
        $objDataDlyTmpl = kernel::single('syslogistics_data_dlytmpl');
        $result = $objDataDlyTmpl->countFee($templateId,$weight,$areaIds);
        return $result;
    }
}
