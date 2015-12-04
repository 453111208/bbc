<?php
class systrade_api_trade_delivery{

    public $apiDescription = "交易发货";
    public function getParams()
    {
        $return['params'] = array(
            'tid' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'订单号'],
            'delivery_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'发货单号'],
            'template_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'配送模板号'],
            'corp_code' => ['type'=>'int', 'valid'=>'required_if:template_id,0', 'default'=>'', 'example'=>'','description'=>'物流公司编号'],
            'logi_no' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'运单号'],
            'shop_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'店铺id'],
            'seller_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'商家操作员id'],
        );
        return $return;
    }
    public function deliveryTrade($params)
    {
        $db = app::get('systrade')->database();
        $db->beginTransaction();
        try
        {
            $objLibTrade = kernel::single('systrade_data_trade');
            $tradeInfo = $objLibTrade->getTradeInfo('post_fee',array('tid'=>$params['tid']));

            //更新发货单状态
            $deliveryData = array(
                'delivery_id' => $params['delivery_id'],
                'template_id' => $params['template_id'],
                'logi_no' => $params['logi_no'],
                'tid' => $params['tid'],
                'post_fee' => $tradeInfo['post_fee'] ? $tradeInfo['post_fee'] : 0,
                'corp_code' => $params['corp_code'],
            );
            $result = app::get('systrade')->rpcCall('delivery.update',$deliveryData);
            $detail = array_bind_key($result['detail'],"oid");


            //更新订单发货状态
            $tradeData = array(
                'tid' => $params['tid'],
                'status' => 'WAIT_BUYER_CONFIRM_GOODS',
                'consign_time' => time(),
            );

            $objMdlOrder = app::get('systrade')->model('order');
            $objMath = kernel::single('ectools_math');
            foreach($tradeInfo['order'] as $value)
            {
                $updateData['sendnum'] = $objMath->number_plus(array($value['sendnum'], $detail[$value['oid']]['number']));
                $updateData['status'] = "WAIT_BUYER_CONFIRM_GOODS";
                $updateData['oid'] = $value['oid'];
                $updateData['consign_time'] = time();
                $isSave = $objMdlOrder->save($updateData);
                if(!$isSave)
                {
                    throw new LogicException("更新子订单发货状态失败");
                }
            }

            $objMdlTrade = app::get('systrade')->model('trade');
            $issave = $objMdlTrade->save($tradeData);
            if(!$issave)
            {
                throw new LogicException("更新订单发货状态失败");
            }
            $db->commit();
        }
        catch (Exception $e)
        {
            $db->rollback();
            throw $e;
        }
        return true;
    }
}
