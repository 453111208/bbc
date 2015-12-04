<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class systrade_data_trade_editprice
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
     * 订单价格修改
     * @params array - 订单修改信息
     * @return boolean - 成功与否
     */
    public function generate($params)
    {
        if(!$params)
        {
            throw new \LogicException("修改订单价格失败，缺少系统参数");
        }

        $objTrade = kernel::single('systrade_data_trade');
        $tradeInfo = $objTrade->getTradeInfo('tid,status,total_fee,discount_fee,adjust_fee,post_fee', array('tid'=>$params['tid']));
        if($tradeInfo['status'] != 'WAIT_BUYER_PAY')
        {
            throw new \LogicException("只有未支付状态的订单才可以修改价格");
        }
        if( $params['post_fee']<0 ){
            throw new \LogicException("邮费价格不能小于0");
        }

        $objMath = kernel::single('ectools_math');
        $aDataTmp = array(
            'tid' => $params['tid'],
            'post_fee' => $params['post_fee'],
        );
        $orderPayment = array();
        $orderAdjustFee = array();
        foreach($tradeInfo['order'] as $k=>$v)
        {
            $aDataTmp['order'][$k]['oid'] = $v['oid'];
            $adjust_fee = $orderAdjustFee[] = $params['order'][$v['oid']]['adjust_fee'] ? $params['order'][$v['oid']]['adjust_fee'] : 0;
            $aDataTmp['order'][$k]['adjust_fee'] = ($adjust_fee >= 0) ? abs($adjust_fee) : $objMath->number_minus(array(0, abs($adjust_fee)));
            if($params['order'][$v['oid']])
            {
                $orderPayment[] = $aDataTmp['order'][$k]['payment'] = $objMath->number_plus( array($v['total_fee'], $adjust_fee) );
                if($aDataTmp['order'][$k]['payment']<=0)
                {
                    throw new \LogicException("调整金额错误，子订单应付金额应大于0");
                }
            }
        }

        $aDataTmp['adjust_fee'] = $objMath->number_plus($orderAdjustFee);
        $aDataTmp['payment'] = $totalFee = $objMath->number_plus($orderPayment);
        $aDataTmp['payment'] = $objMath->number_plus(array($aDataTmp['payment'], $aDataTmp['post_fee']));

        //计算商品总额所获积分
        $subtotal_obtain_point = app::get('systrade')->rpcCall('user.pointcount',array('money'=>$totalFee));
        $aDataTmp['obtain_point_fee'] = $subtotal_obtain_point;

        if($aDataTmp['payment']<0)
        {
            throw new \LogicException("修改金额错误，主订单应付金额应大于0");
        }

        $db = app::get('systrade')->database();
        $transaction_status = $db->beginTransaction();

        try
        {
            if(!app::get('systrade')->model('trade')->save($aDataTmp))
            {
                throw new \LogicException("取消订单失败，更新数据库失败");
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
     * 记录订单价格修改日志
     * @param int &$canCancelTid 订单数据[操作者信息]
     * @param array &$params       成功标识
     */
    private function addLog(&$tid, &$params)
    {
        $objLibLog = kernel::single('systrade_data_trade_log');
        $logText = '修改订单价格成功！';
        $sdfTradeLog = array(
            'rel_id'   => $tid,
            'op_id'    => $params['operator']['op_id'],
            'op_name'  => $params['operator']['op_account'] ? $params['operator']['op_account'] : '未知',
            'op_role'  => $params['operator']['account_type'],
            'behavior' => 'update',
            'log_text' => $logText,
        );

        if(!$objLibLog->addLog($sdfTradeLog))
        {
            return false;
        }

        return true;
    }


}
