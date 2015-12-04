<?php
class ectools_api_getPaymentBill{

    public $apiDescription = "获取支付单信息";
    public function getParams()
    {
        $return['params'] = array(
            'payment_id' => ['type'=>'string','valid'=>'','description'=>'支付单编号','default'=>'','example'=>''],
            'tids' => ['type'=>'string','valid'=>'','description'=>'被支付的订单号','default'=>'','example'=>''],
            'status' => ['type'=>'string','valid'=>'','description'=>'支付状态,多个状态使用逗号隔开','default'=>'','example'=>''],
            'fields' => ['type'=>'field_list','valid'=>'','description'=>'支付单字段','default'=>'','example'=>''],
        );
        return $return;
    }
    public function getInfo($params)
    {
        $filter = array();
        $paymenList = array();
        $objPayment = kernel::single('ectools_data_payment');
        $row = $params['fields'];
        if($params['payment_id'])
        {
            $filterBill['payment_id'] = $filter['payment_id'] = $params['payment_id'];
        }
        if($params['tids'])
        {
            $filterBill['tid'] = explode(',',$params['tids']);
        }
        if($params['status'])
        {
            $filter['status|in'] = explode(',',$params['status']);
        }
        if($filter)
        {
            $objMdlPayment = app::get('ectools')->model('payments');
            $paymentBill = $objMdlPayment->getRow($row,$filter);
        }

        if($filterBill)
        {
            $objMdlTradePaybill = app::get('ectools')->model('trade_paybill');
            $billList = $objMdlTradePaybill->getList('payment_id,tid,payment,status',$filterBill);
        }

        if($paymentBill && $billList)
        {
            foreach($billList as $val)
            {
                $paymentBill['trade'][$val['tid']] = $val;
            }
        }
        return $paymentBill;
    }
}
