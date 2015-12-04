<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class systrade_data_trade_cancel
{

    /**
     * 最终的克隆方法，禁止克隆本类实例，克隆是抛出异常。
     * @params null
     * @return null
     */
    final public function __clone()
    {
        trigger_error(app::get('systrade')->_("此类对象不能被克隆！"), E_USER_ERROR);
    }

    /**
     * 订单取消
     * @params array - 订单数据
     * @params object - 控制器
     * @params string - 支付单生成的记录
     * @return boolean - 成功与否
     */
    public function generate($params)
    {
        if(!$params['data'])
        {
            $msg = "取消订单失败，缺少系统参数";
            throw new \LogicException($msg);
            return false;
        }

        if(!$params['data']['cancel_reason'])
        {
            $msg = "取消订单失败，取消原因必填";
            throw new \LogicException($msg);
            return false;
        }

        if(!$filter = $params['filter'])
        {
            $msg = "取消订单失败，缺少条件参数";
            throw new \logicexception($msg);
            return false;
        }

       $param['filter'] = $filter;

        //获取未付款的订单
        if(isset($filter['tid']) && $filter['tid'])
        {
            $filters = $param['filter']['tid'] = explode(',',$filter['tid']);
        }

        $param['filter']['status'] =array('WAIT_BUYER_PAY');
        $param['rows'] ="tid,status";

        $objTrade = kernel::single('systrade_data_trade');
        $tradeList = $objTrade->getTradeList($param,false);
        if(!$tradeList)
        {
            if(isset($filters) && $filters)
            {
                $notCancel = implode(',',$filters);
                $msg = "取消订单失败，订单‘".$notCancel."’已被取消或者已付款不可再做取消处理";
            }
            else
            {
                $msg = "不存在要取消的订单";
            }
            throw new \logicexception($msg);
            return false;
        }

        foreach($tradeList as $key=>$value)
        {
            $canCancel[] = $value['tid'];
            /*
            foreach($value['order'] as $k=>$val)
            {
                $canCancelOid[] = $val['oid'];
            }
             */
        }

        //获取不能被取消的订单号
        if(isset($filters) && $filters)
        {
            $notCancel = array_diff($filters,$canCancel);
            if($notCancel)
            {
                $notcancel = implode(',',$notCancel);
                $msg = "取消订单失败，订单‘".$notcancel."’在处理中，不可取消";
                throw new \LogicException($msg);
                return false;
            }
        }

        $params['filter']['tid'] = $canCancel;

        $objMdlOrder = app::get('systrade')->model('order');
        $orderParams = array(
            'status' =>'TRADE_CLOSED_BEFORE_PAY',
            'end_time' => time(),
        );

        $db = app::get('systrade')->database();
        $db->beginTransaction();

        try
        {
            if(!$objTrade->updateTrade($params))
            {
                throw new \Exception("取消订单失败，更新数据库失败");
            }

            if(!$objMdlOrder->update($orderParams,$params['filter']))
            {
                throw new \Exception("取消订单失败，更新数据库失败");
            }

            // 恢复、解冻库存等
            foreach($canCancel as $canCancelTid)
            {
                // 返还优惠券，如果有的情况下
                if( !app::get('systrade')->rpcCall('user.coupon.back', array('tid'=>$canCancelTid)) )
                {
                    throw new \Exception("取消订单{$canCancelTid}失败，退还优惠券失败");
                }

                if( !$this->recoverStore($canCancelTid) )
                {
                    throw new \Exception("取消订单{$canCancelTid}失败，恢复库存失败");
                }

                if(!$this->addLog($canCancelTid, $params))
                {
                    throw new \Exception("取消订单{$canCancelTid}失败，更新数据库失败[取消日志]");
                }
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

    private function recoverStore($tid)
    {
        $isRecover = true;
        $orderInfo = app::get('systrade')->model('order')->getList('oid,shop_id,status,item_id,sku_id,num,sub_stock', array('tid'=>$tid));
        foreach ($orderInfo as $oVal)
        {
            $arrParam = array(
                'item_id'  => $oVal['item_id'],
                'sku_id'   => $oVal['sku_id'],
                'quantity' => $oVal['num'],
                'sub_stock' => $oVal['sub_stock'],
            );
            $isRecover = app::get('systrade')->rpcCall('item.store.recover',$arrParam);
            if(!$isRecover) return false;
        }

        return $isRecover;
    }

    /**
     * 记录订单取消日志
     * @param int &$canCancelTid 订单数据[操作者信息]
     * @param array &$params       成功标识
     */
    private function addLog(&$canCancelTid, &$params)
    {
        $objLibLog = kernel::single('systrade_data_trade_log');
        $logText = '取消订单成功！';
        $sdfTradeLog = array(
            'rel_id'   => $canCancelTid,
            'op_id'    => $params['operator']['op_id'],
            'op_name'  => $params['operator']['op_account'] ? $params['operator']['op_account'] : '未知',
            'op_role'  => $params['operator']['account_type'],
            'behavior' => 'cancel',
            'log_text' => $logText,
        );

        if(!$objLibLog->addLog($sdfTradeLog))
        {
            return false;
        }

        return true;
    }


}
