<?php
class ectools_api_getPayments{
    public $apiDescription = "获取可用支付方式列表";
    public function getParams()
    {
        $return['params'] = array(
            'platform' => ['type'=>'string','valid'=>'required|string','description'=>'支付方式使用平台pc、wap','default'=>'ispc','example'=>''],
        );
        return $return;
    }
    public function getList($params)
    {
        $objPayment = kernel::single('ectools_data_payment');
        $platform = array('iscommon');
        if($params['platform'])
        {
            array_unshift($platform,$params['platform']);
        }
        $payments = $objPayment->getPayments('CNY',['iscommon',$params['platform']]);
        return $payments;
    }
}
