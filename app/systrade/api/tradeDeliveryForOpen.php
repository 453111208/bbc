<?php
class systrade_api_tradeDeliveryForOpen{
    public $apiDescription = "订单发货状态变更(联通erp)";
    public function getParams()
    {
        $return['params'] = array(
            'tid' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'订单号'],
            'items' => ['type'=>'string', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'发货详情[{"oid":"子订单号","number":11},{"oid":"子订单号","number":11}]'],
        //  'shop_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'店铺id'],
        );
        return $return;
    }
    public function doDelivery($params, $oauth)
    {
        if($oauth['shop_id'])
        {
            $params['shop_id'] = $oauth['shop_id'];
        }

        $item = json_decode($params['items'],true);
        unset($params['items']);
        $item = $this->__checkItems($item);
        if(!$item)
        {
            throw new LogicException('发货明细不存在');
        }
        $oids = array_column($item,'oid');

        $db = app::get('systrade')->database();
        $db->beginTransaction();
        try
        {
            //更新订单发货状态
            $tradeData = array(
                'tid' => $params['tid'],
                'status' => 'WAIT_BUYER_CONFIRM_GOODS',
                'consign_time' => time(),
            );

            $objMdlOrder = app::get('systrade')->model('order');
            $orders = $objMdlOrder->getList('sendnum,oid',array('oid'=>$oids));
            $orders = array_bind_key($orders,'oid');
            $objMath = kernel::single('ectools_math');
            foreach($item as $value)
            {
                $updateData['sendnum'] = $objMath->number_plus(array($value['number'], $orders[$value['oid']]['sendnum']));
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

    private function __checkItems($item)
    {
        if(!is_array($item))
        {
            throw new LogicException('item参数格式不正确');
        }
        $deliveryDetailValidate = array(
            'oid' =>'required',
            'number' =>'required|numeric',
        );
        foreach($item as $k=> $val)
        {
            $validator = validator::make($val, $deliveryDetailValidate);
            if( $validator->fails() )
            {
                $errors = json_decode( $validator->messages(), 1 );
                foreach( $errors as $error )
                {
                    throw new LogicException( $error[0] );
                }
            }
            $newItem[$k]['oid']       =  $val['oid'];
            $newItem[$k]['number']    =  $val['number'];
        }
        return $newItem;
    }


}
