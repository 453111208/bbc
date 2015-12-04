<?php
class systrade_api_trade_addMemo{
    public $apiDescription = "订单备注添加";
    public function getParams()
    {
        $return['params'] = array(
            'tid' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'订单id'],
            'shop_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'订单所属店铺id'],
            'trade_memo' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'备注内容'],
            'shop_memo' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'备注内容'],
        );
        return $return;
    }
    public function add($params)
    {
        if($params['trade_memo'])
        {
            $data['trade_memo'] = $params['trade_memo'];
        }
        if($params['shop_memo'])
        {
            $data['shop_memo'] = $params['shop_memo'];
        }

        $filter['tid'] = $params['tid'];
        $filter['shop_id'] = $params['shop_id'];
        $objMdlTrade = app::get('systrade')->model('trade');
        $result = $objMdlTrade->update($data,$filter);
        return $result;
    }
}
