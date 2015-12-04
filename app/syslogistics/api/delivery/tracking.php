<?php
class syslogistics_api_delivery_tracking{
    public $apiDescription = "获取订单发货物流信息";
    public function getParams()
    {
        $return['params'] = array(
            'tid' =>['type'=>'string','valid'=>'required', 'description'=>'订单号','default'=>'','example'=>''],
        );
        return $return;
    }
    public function getTracking($params)
    {
        $tid = $params['tid'];
        $rows = 'logi_name,logi_no,corp_code,delivery_id,receiver_name';
        $data = app::get('syslogistics')->model('delivery')->getRow($rows, array('tid'=>$tid,'status'=>'succ'), 0, 1);
        if(!$data)
        {
            return false;
        }

        if($data['logi_no'] && $data['corp_code'])
        {
            try{
                $data['tracker'] = kernel::single('syslogistics_data_tracker')->pullFromHqepay($data['logi_no'],$data['corp_code']);
            }catch(Exception $e){
                $data['logmsg'] = $e->getMessage();
            }
        }
        return $data;
    }
}
