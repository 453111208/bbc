<?php
class syslogistics_api_dlytmpl_get{
    public $apiDescription = " 获取运费模板列表";
    public function getParams()
    {
        $return['params'] = array(
            'shop_id' =>['type'=>'string','valid'=>'', 'description'=>'店铺id','default'=>'','example'=>'1'],
            'template_id' =>['type'=>'string','valid'=>'', 'description'=>'模板id','default'=>'','example'=>'1'],
            'status' =>['type'=>'string','valid'=>'', 'description'=>'模板状态','default'=>'on','example'=>'on'],
            'name' =>['type'=>'string','valid'=>'', 'description'=>'模板名称','default'=>'on','example'=>'on'],
            'fields' =>['type'=>'string','valid'=>'', 'description'=>'所需字段','default'=>'template_id,name,valuation,corp_id,protect,protect_rate,fee_conf,minprice','example'=>'name,status'],
        );
        return $return;
    }
    public function getList($params)
    {
        if($params['template_id'])
        {
            $filter['template_id'] = $params['template_id'];
        }

        if($params['shop_id'])
        {
            $filter['shop_id'] = $params['shop_id'];
        }

        if($params['status'])
        {
            $filter['status'] = $params['status'] ;
        }

        if($params['name'])
        {
            $filter['name'] = $params['name'] ;
        }

        if(!$filter)
        {
            return false;
        }

        $row = "template_id,name,valuation,corp_id,protect,protect_rate,fee_conf,minprice";
        if($params['fields'])
        {
            $row = $params['fields'];
        }
        $objDataDlyTmpl = kernel::single('syslogistics_data_dlytmpl');
        $data = $objDataDlyTmpl->getRow($row,$filter);
        return $data;
    }
}
