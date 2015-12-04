<?php
class sysitem_api_item_minusStore{
    public $apiDescription = "扣减库存";
    public function getParams()
    {
        $return['params'] = array(
            'item_id' => ['type'=>'int','valid'=>'required','description'=>'商品id','example'=>'2','default'=>''],
            'sku_id' => ['type'=>'int','valid'=>'required','description'=>'货品id','example'=>'2','default'=>''],
            'quantity' => ['type'=>'int','valid'=>'required','description'=>'扣减库存数量','example'=>'2','default'=>''],
            'sub_stock' => ['type'=>'int','valid'=>'','description'=>'扣减库存方式(order_create,order_pay)','example'=>'2','default'=>''],
            'status' => ['type'=>'int','valid'=>'','description'=>'订单支付状态(on,success)','example'=>'2','default'=>''],
        );
        return $return;
    }
    public function storeMinus($params)
    {
        $subStock = $params['sub_stock'];
        $status = $params['status'];

        $objLibStore = kernel::single('sysitem_trade_store');
        if ($subStock)
        {
            // 下单减库存，有恶意下单占库存风险
            if(!$is_minus = $objLibStore->minusItemStore($params))
            {
                $msg = app::get('sysitem')->_('商品库存不足');
                throw new \LogicException($msg);
                return false;
            }
        }
        else
        {
            if($status == "on")
            {
                // 付款减库存，有库存超卖风险
                if(!$is_freez = $objLibStore->freezeItemStore($params))
                {
                    $msg = app::get('sysitem')->_('商品库存不足');
                    throw new \LogicException($msg);
                    return false;
                }
            }
            elseif($status = "success")
            {
                $result = $objLibStore->minusItemStoreAfterPay($params);
                if(!$result)
                {
                    $msg = app::get('sysitem')->_('库存扣减失败');
                    throw new \LogicException($msg);
                    return false;
                }
            }
        }
        return true;
    }
}
