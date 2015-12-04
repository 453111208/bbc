<?php
class syslogistics_api_dlytmpl_add{

    public $apiDescription = "运费模板添加";
    public function getParams()
    {
        $return['params'] = array(
            'name' =>['type'=>'string','valid'=>'required', 'description'=>'模板名称','default'=>'','example'=>'1'],
            'shop_id' =>['type'=>'int','valid'=>'required', 'description'=>'所属店铺id','default'=>'','example'=>'1'],
            'corp_id' =>['type'=>'int','valid'=>'required', 'description'=>'关联的物流公司','default'=>'','example'=>'1'],
            'order_sort' =>['type'=>'int','valid'=>'', 'description'=>'排序','default'=>'','example'=>'1'],
            'status' =>['type'=>'string','valid'=>'', 'description'=>'模板状态','default'=>'','example'=>'1'],
            'valuation' =>['type'=>'string','valid'=>'', 'description'=>'运费计算参数来源','default'=>'','example'=>'1'],
            'protect' =>['type'=>'string','valid'=>'', 'description'=>'物流保价','default'=>'','example'=>'1'],
            'protect_rate' =>['type'=>'string','valid'=>'', 'description'=>'保价费率','default'=>'','example'=>'1'],
            'minprice' =>['type'=>'string','valid'=>'', 'description'=>'保价费最低值','default'=>'','example'=>'1'],
            'create_time' =>['type'=>'string','valid'=>'', 'description'=>'模板添加时间','default'=>'','example'=>'1'],
            'fee_conf' =>['type'=>'json','valid'=>'', 'description'=>'配送地区配置','default'=>'','example'=>'1'],
        );
        return $return;
    }
    public function create($params)
    {
        if($params['fee_conf'])
        {
            $params['fee_conf'] = json_decode($params['fee_conf'],true);
        }

        $shopId = $params['shop_id'];

        $objDataDlyTmpl = kernel::single('syslogistics_data_dlytmpl');
        $result = $objDataDlyTmpl->addDlyTmpl($params,$shopId);
        return $result;
    }
}
