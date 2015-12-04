<?php
class systrade_api_tradeMoney{

    public $apiDescription = '获取指定订单的金额及总和';
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'tid' => ['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'支付单中订单号合集字符串'],
        );
        return $return;
    }

    public function getList($params)
    {
        $filter['tid'] = explode(',',$params['tid']);
        $objMdlTrade = app::get('systrade')->model('trade');
        $trades = $objMdlTrade->getList('tid,payment',$filter);
        return $trades;
    }
}
