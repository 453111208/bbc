<?php
class ectools_api_getPaymentConf{
    public $apiDescription = "获取可用支付方式的配置信息";
    public function getParams()
    {
        $return['params'] = array(
            'app_id' => ['type'=>'string','valid'=>'required|string','description'=>'支付方式的id','default'=>'','example'=>'wxpayjsapi'],
        );
        return $return;
    }
    public function getConf($params)
    {
        $objPayment = app::get('ectools')->model('payment_cfgs');
        $rows = 'app_class';
        $filter = ['app_id'=>$params['app_id']];
        $payments = $objPayment->getRow($rows,$filter);
        $class = $payments['app_class'];
        $conf = app::get('ectools')->getConf($class);
        $conf = unserialize($conf);
        return $conf;
    }
}
