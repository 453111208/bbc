<?php
class syslogistics_api_delivery_update{

    public $apiDescription = "发货单更新";
    public function getParams()
    {
        $return['params'] = array(
            'delivery_id' =>['type'=>'string','valid'=>'required', 'description'=>'发货单流水编号','default'=>'','example'=>'1'],
            'template_id' =>['type'=>'string','valid'=>'required', 'description'=>'运费模板号','default'=>'','example'=>'1'],
            'corp_code' =>['type'=>'string','valid'=>'required_if:template_id,0', 'description'=>'物流公司代码','default'=>'','example'=>'1'],
            'logi_no' =>['type'=>'string','valid'=>'required', 'description'=>'运单号','default'=>'','example'=>'1'],
            'tid' =>['type'=>'string','valid'=>'required', 'description'=>'订单号','default'=>'','example'=>'1'],
            'post_fee' =>['type'=>'string','valid'=>'required', 'description'=>'运费','default'=>'','example'=>'1'],
        );
        return $return;
    }
    public function update($params)
    {
        $objMdlDelivery = app::get('syslogistics')->model('delivery');
        $objMdlDeliveryDetail = app::get('syslogistics')->model('delivery_detail');
        $data = $objMdlDelivery->getRow('*',array('delivery_id' => $params['delivery_id']));
        if($data)
        {
            $data['detail'] = $objMdlDeliveryDetail->getList('*',array('delivery_id' => $params['delivery_id']));
        }
        if(!$data)
        {
            throw new LogicException('发货失败，发货单有误');
        }

        if($params['template_id'])
        {
            $objMdlDlytmpl = app::get('syslogistics')->model('dlytmpl');
            $objMdlDlyCorp = app::get('syslogistics')->model('dlycorp');
            $tmpl = $objMdlDlytmpl->getRow('corp_id',array('template_id' => $params['template_id']));
            $corp = $objMdlDlyCorp->getRow('corp_name,corp_code,corp_id',array('corp_id'=>$tmpl['corp_id']));
        }
        else
        {
            $objMdlDlyCorp = app::get('syslogistics')->model('dlycorp');
            $corp = $objMdlDlyCorp->getRow('corp_name,corp_code,corp_id',array('corp_code'=>$params['corp_code']));
        }

        $delivery['corp_code'] = $corp['corp_code'];
        $delivery['logi_id'] = $corp['corp_id'];
        $delivery['logi_name'] = $corp['corp_name'];
        $delivery['logi_no'] = trim($params['logi_no']);
        $delivery['delivery_id'] = $params['delivery_id'];
        $delivery['dlytmpl_id'] = $params['template_id'];
        $delivery['post_fee'] = $params['post_fee'];
        $delivery['is_protect'] = 0;
        $delivery['memo'] = "";
        $delivery['status'] = "succ";
        $delivery['t_send'] = time();
        $delivery['t_confirm'] = time();

        $isSave = $objMdlDelivery->update($delivery,array('delivery_id'=>$params['delivery_id'],'tid'=>$params['tid']));
        if(!$isSave)
        {
            throw new LogicException('更新订单发货单失败');
        }
        return $data;
    }
}
