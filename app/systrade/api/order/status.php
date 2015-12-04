<?php
class systrade_api_order_status{
    /**
     * 接口作用说明
     */
    public $apiDescription = '订单售后状态处理(未完成订单退款成功时更新订单状态为关闭)';

    /**
     * 定义应用级参数，参数的数据类型，参数是否必填，参数的描述
     * 用于在调用接口前，根据定义的参数，过滤必填参数是否已经参入
     */
    public function getParams()
    {
        //接口传入的参数
        $return['params'] = array(
            'tid' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'主订单编号'],
            'oid' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'子订单编号'],
            'user_id' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'订单所属用户'],
            'aftersales_status' => ['type'=>'int', 'valid'=>'required', 'default'=>'', 'example'=>'','description'=>'子订单售后状态'],
            'refund_fee' => ['type'=>'string', 'valid'=>'', 'default'=>'', 'example'=>'','description'=>'子订单退款金额'],
        );

        return $return;
    }

    /**
     * 获取单笔交易数据
     *
     * @param array $params 接口传入参数
     * @return array
     */

    public function update($params)
    {
        $objMdlTrade = app::get('systrade')->model('trade');
        $objMdlOrder = app::get('systrade')->model('order');
        $ifAll = false;
        $orderData = array();
        $tradesData = array();

        //数据监测
        try
        {
            $data = $this->__check($params,$orderData,$tradesData,$ifAll);
        }
        catch(\LogicException $e)
        {
            throw new \LogicException(app::get('systrade')->_($e->getMessage()));
        }

        $db = app::get('systrade')->database();
        $db->beginTransaction();

        try
        {
            //当平台退款完成时需要改变订单状态
            if($ifAll && $tradesData && $tradesData['status'] != "TRADE_FINISHED")
            {
                $updataTradeData['status'] = "TRADE_CLOSED";
                $updataTradeData['tid'] = $params['tid'];
                $updataTradeData['cancel_reason'] = "退款成功，交易自动关闭";
                $result = $objMdlTrade->update($updataTradeData,array('tid'=>$params['tid']));
                if(!$result)
                {
                    throw new \LogicException(app::get('systrade')->_('退款失败，关闭订单异常'));
                }
            }

            //子订单售后状态改变
            $result = $objMdlOrder->update($data,array('oid'=>$params['oid']));
            if(!$result)
            {
                throw new \LogicException(app::get('systrade')->_('退款失败，订单状态更新失败'));
            }

            if($orderData)
            {
                if($orderData['status'] == "TRADE_FINISHED")
                {
                    $orderData['refund_fee'] = $params['refund_fee'];
                    //处理积分回扣和经验值回扣
                    $this->__PointAndExp($orderData);

                    //只有在订单完成后的售后退款才需要进行，结算退款处理
                    $this->__settlement($tradesData, $orderData);
                }
            }
            $db->commit();
        }
        catch(\Exception $e)
        {
            $db->rollback();
            throw $e;
        }
        return true;
    }

    private function __PointAndExp($orderData)
    {
        $num = app::get('systrade')->rpcCall('user.pointcount',array('money'=>$orderData['refund_fee']));
        $pointdata = $expdata = array(
            'user_id' => $orderData['user_id'],
            'type' => 'consume',
            'num' => $num,
            'behavior' => '退款回退积分',
            'remark' => "订单： ".$orderData['tid'],
        );
        $result = app::get('systrade')->rpcCall('user.updateUserPoint',$pointdata);
        if(!$result)
        {
            throw new \LogicException(app::get('systrade')->_('退款失败，关闭订单异常'));
        }

        $expdata['behavior'] = "退款回退经验值";
        $expdata['num'] = $orderData['payment'];
        $result = app::get('systrade')->rpcCall('user.updateUserExp',$expdata);
        if(!$result)
        {
            throw new \LogicException(app::get('systrade')->_('退款失败，关闭订单异常'));
        }
    }

    private function __check($params,&$orderData,&$tradesData,&$ifAll)
    {
        $objMdlTrade = app::get('systrade')->model('trade');
        $objMdlOrder = app::get('systrade')->model('order');

        if($params['aftersales_status'] == "SUCCESS")
        {
            if(!$params['refund_fee']) throw new \LogicException(app::get('systrade')->_('数据有误'));
            $ifAll = true;
            $settlementFields = 'shop_id,item_id,sku_id,bn,title,spec_nature_info,price,num,sku_properties_name,divide_order_fee,part_mjz_discount,payment,refund_fee,cat_service_rate,discount_fee,adjust_fee';
            $orders = $objMdlOrder->getList('tid,oid,status,aftersales_status,user_id,payment,'.$settlementFields,array('tid'=>$params['tid']));

            foreach($orders as $key=>$order)
            {
                if($order['user_id'] != $params['user_id'])
                {
                    throw new \LogicException(app::get('sysaftesystradersales')->_('数据有误，请重新处理'));
                    return false;
                }

                if(!$order['aftersales_status'])
                {
                    $ifAll = false;
                }

                if($order['oid'] == $params['oid'])
                {
                    $orderData = $order;
                }
            }

            $tradesData = $objMdlTrade->getRow('status,payment,pay_time,post_fee',array('tid'=>$params['tid']));

            if($order['oid'] == $params['oid'] && $order['status'] != "TRADE_FINISHED")
            {
                $data['status'] = "TRADE_CLOSED_AFTER_PAY";
                $data['refund_fee'] = $params['refund_fee'];
            }
        }
        $data['aftersales_status'] = $params['aftersales_status'];
        return $data;

    }

    //商家结算
    private function __settlement($tradeData, $orderData)
    {
        $data = $tradeData;
        $data['order'][0] = $orderData;
        $result = kernel::single('sysclearing_settlement')->generate($data,'3');
        if(!$result)
        {
            throw new \LogicException(app::get('systrade')->_('退款失败，商家结算明细生成失败'));
        }
        return true;
    }
}
