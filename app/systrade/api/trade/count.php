<?php
class systrade_api_trade_count{
    public $apiDescription = "根据条件统计订单数量";
    public  function getParams()
    {
        $return['params'] = array(
            'user_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'会员id'],
            'shop_id' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'店铺id'],
            'status' => ['type'=>'int', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'订单状态'],
        );
        return $return;
    }
    public function tradeCount($params)
    {
        unset($params['oauth']);
        if($params)
        {
            foreach($params as $key=>$value)
            {
                if(!$value)
                {
                    unset($params[$key]);
                }
            }
        }

        $objMdlTrade = app::get('systrade')->model('trade');
        $count = $objMdlTrade->count($params);
        if(!$count)
        {
            $count = 0;
        }
        return $count;

    }
}
