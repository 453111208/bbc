<?php
class systrade_api_trade_notRateCount{
    public $apiDescription = "统计订单未评价数量";
    public function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'会员id'],
            'shop_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'店铺id'],
        );
        return $return;
    }
    public function count($params)
    {
        if($params['user_id'])
        {
            $filter['user_id'] = $params['user_id'];
        }
        if($params['shop_id'])
        {
            $filter['shop_id'] = $params['shop_id'];
        }
        $filter['status'] = "TRADE_FINISHED";
        $objMdlOrder = app::get('systrade')->model('order');
        $count = 0 ;
        $orders = $objMdlOrder->getList('tid,oid,status,aftersales_status,buyer_rate',$filter);
        if($orders)
        {
            foreach($orders as $value)
            {
                $arr[$value['tid']] = 0;
                if(!$value['aftersales_status'] && $value['buyer_rate'] == '0')
                {
                    $arr[$value['tid']] = 1;
                }
            }
            $count = array_sum($arr);
        }
        return $count;
    }
}
