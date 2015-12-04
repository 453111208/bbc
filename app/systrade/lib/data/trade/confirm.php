<?php
class systrade_data_trade_confirm{

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
     * 订单确认完成
     * @params array - 订单数据
     * @params object - 控制器
     * @params string - 支付单生成的记录
     * @return boolean - 成功与否
     */
    public function generate($params)
    {
        if(!$params['data'])
        {
            $msg = "完成订单失败，缺少系统参数";
            throw new \LogicException($msg);
            return false;
        }

        if(!$params['filter'])
        {
            $msg = "完成订单失败，缺少条件参数";
            throw new \LogicException($msg);
            return false;
        }

        $objTrade = kernel::single('systrade_data_trade');

        $tradeList = $objTrade->getTradeInfo('status',$params['filter']);
        if(!$tradeList)
        {
            throw new \LogicException("没有需要完成的订单!");
        }

        if($tradeList['status'] != "WAIT_BUYER_CONFIRM_GOODS")
        {
            throw new \LogicException("订单完成失败，未发货订单不可确认收货");
        }

        $db = app::get('systrade')->database();
        $db->beginTransaction();
        try
        {
            if(!$objTrade->updateTrade($params))
            {
                throw new \Exception("订单完成失败，更新数据库失败");
            }


            // 确认收货后需要处理的事情
            $tradeInfo = $objTrade->getTradeInfo('*',$params['filter']);
            foreach(kernel::servicelist("systrade_trade_confirm_after") as $k=>$object)
            {
                if(is_object($object))
                {
                    if(method_exists($object,'generate'))
                    {
                        if(!$object->generate($tradeInfo))
                        {
                            throw new \Exception("确认收货后需要处理的事情失败");
                        }
                    }
                }
            }

            $objMdlOrder = app::get('systrade')->model('order');
            if(!$objMdlOrder->update( array('status'=>'TRADE_FINISHED','end_time'=>time()), array('tid'=>$tradeInfo['tid']) ) )
            {
                throw new \Exception("订单的子订单完成失败，更新数据库失败");
            }

            // 修改商品销量
            if($this->updateSoldQuantity($tradeInfo))
            {
                $msg = "确认订单失败[销量统计失败]";
                throw new \Exception($msg = "确认订单失败[销量统计失败]");
            }

            //积分确认
            if(!$this->confirmPoint($tradeInfo))
            {
                throw new \Exception("确认订单失败[会员积分结算失败]");
            }

            //经验值确认
            if(!$this->confirmExperience($tradeInfo))
            {
                throw new \Exception("确认订单失败[会员经验值结算失败]");
            }

            if( !$this->addLog($params, $result) )
            {
                throw new \Exception("确认订单失败[日志]");
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

    /**
     * 记录订单确认收货日志
     * @param array &$tradeData 订单数据
     * @param bool $flag       订单成功标识
     */
    private function addLog(&$tradeData, &$flag)
    {
        $objLibLog = kernel::single('systrade_data_trade_log');
        $logText = $flag ? '确认订单成功！' : '确认订单失败！';
        $sdfTradeLog = array(
            'rel_id'   => $tradeData['filter']['tid'],
            'op_id'    => $tradeData['operator']['op_id'],
            'op_name'  => $tradeData['operator']['op_account'] ? $tradeData['operator']['op_account'] : '未知',
            'op_role'  => $tradeData['operator']['account_type'],
            'behavior' => 'confirm',
            'log_text' => $logText,
        );

        if(!$objLibLog->addLog($sdfTradeLog))
        {
            return false;
        }

        return true;
    }

    /**
     * 调用修改商品销量接口
     * @param  array &$tradeInfo 订单信息
     * @return
     */
    private function updateSoldQuantity(&$tradeInfo)
    {
        if($tradeInfo && is_array($tradeInfo['order']))
        {
            foreach($tradeInfo['order'] as $key => $val)
            {
                $apiData = array('item_id'=>$val['item_id'], 'num'=>$val['num']);
                if(!app::get('systrade')->rpcCall('item.updateSoldQuantity', $apiData))
                {
                    return false;
                }
            }
        }
        else
        {
            return false;
        }
    }

    /**
     * @brief 确认会员积分
     *
     * @param $tradeInfo
     *
     * @return
     */
    private function confirmPoint(&$tradeInfo)
    {
        if(!$tradeInfo) return false;

        $params['user_id'] = $tradeInfo['user_id'];
        $params['type'] = "obtain";
        $params['behavior'] = "购物获得积分";
        $params['remark'] = "当前积分来自订单：".$tradeInfo['tid'];
        if($tradeInfo['consume_point_fee'])
        {
            $params['type'] = "consume";
            $params['behavior'] = "购物消费积分";
            $params['remark'] = "当前积分由订单：".$tradeInfo['tid']."消费";
        }
        $params['num'] = $tradeInfo['obtain_point_fee'];

        $result = app::get('systrade')->rpcCall('user.updateUserPoint',$params);
        if(!$result) return false;
        return true;
    }

    /**
     * @brief 确认会员经验值
     *
     * @param $tradeInfo
     *
     * @return
     */
    private function confirmExperience(&$tradeInfo)
    {
        if(!$tradeInfo) return false;

        $params['user_id'] = $tradeInfo['user_id'];
        $params['type'] = "obtain";
        $params['num'] = $tradeInfo['payment']-$tradeInfo['post_fee'];
        $params['behavior'] = "购物获得经验值";
        $params['remark'] = "当前经验值来自订单：".$tradeInfo['tid'];

        $result = app::get('systrade')->rpcCall('user.updateUserExp',$params);
        if(!$result) return false;
        return true;
    }


}
