<?php
class sysitem_api_item_recoverStore{
    public $apiDescription = "恢复库存";
    public function getParams()
    {
        $return['params'] = array(
            'item_id' => ['type'=>'int','valid'=>'required','description'=>'商品id','example'=>'2','default'=>''],
            'sku_id' => ['type'=>'int','valid'=>'required','description'=>'货品id','example'=>'2','default'=>''],
            'quantity' => ['type'=>'int','valid'=>'required','description'=>'恢复库存数量','example'=>'2','default'=>''],
            'sub_stock' => ['type'=>'bool','valid'=>'','description'=>'扣减库存方式(order_create,order_pay)','example'=>'2','default'=>''],
        );
        return $return;
    }
    public function storeRecover($params)
    {
        $subStock = $params['sub_stock'];
        unset($params['sub_stock']);
        if($subStock)
        {
            $isRecover = kernel::single('sysitem_trade_store')->recoverItemStore($params);
            if(!$isRecover)return false;
        }
        else
        {
            $isRecover = kernel::single('sysitem_trade_store')->unfreezeItemStore($params);
            if(!$isRecover)return false;
        }
        return true;
    }
}
