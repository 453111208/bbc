<?php

/**
 * @brief 商品数据处理
 */
class sysclearing_settlement{

    /**
     *确认收货的时候处理结算明细
     *
     * @param array $tradeInfo 订单数据
     * @param int $settlementType 结算类型 1 为普通结算，2 运费结算，3 退款结算
     */
    function generate($tradeInfo, $settlementType='1')
    {
        if($tradeInfo && is_array($tradeInfo['order']))
        {
            $objMdlSettleDetail = app::get('sysclearing')->model('settlement_detail');
            $objLibMath = kernel::single('ectools_math');
            foreach($tradeInfo['order'] as $key => $val)
            {
                $data = array(
                    'oid' => $val['oid'],
                    'tid' => $val['tid'],
                    'shop_id' => $val['shop_id'],
                    'settlement_time' => time(),
                    'pay_time' => $tradeInfo['pay_time'],
                    'item_id' => $val['item_id'],
                    'sku_id' => $val['sku_id'],
                    'bn' => $val['bn'],
                    'title' => $val['title'],
                    'spec_nature_info' => $val['spec_nature_info'],
                    'price' => $val['price'],
                    'num' => $val['num'],
                    'sku_properties_name' => $val['sku_properties_name'],
                    'divide_order_fee' => $val['divide_order_fee'],
                    'part_mjz_discount' => $val['part_mjz_discount'],
                    'payment' => $val['payment'],
                    'refund_fee' => $val['refund_fee'],
                    'cat_service_rate' => $val['cat_service_rate'],
                    'settlement_type' => $settlementType,
                    'discount_fee' => $val['discount_fee'],
                    'adjust_fee' => $val['adjust_fee'],
                );
                if($key == '0')//将运费赋值到第一条子订单中
                {
                    $data['post_fee'] = $tradeInfo['post_fee'];
                }else{
                    $data['post_fee'] = 0;
                }

                if( $settlementType == '3' )//如果子订单有部分售后退款的情况，需要改造此处
                {
                    //平台提取的佣金返还
                    $commissionFee = $objLibMath->number_multiple(array($val['refund_fee'],$val['cat_service_rate']));
                    $data['commission_fee'] = -$commissionFee;
                    //返还结算给商家的金额
                    $settlementFee = $objLibMath->number_minus(array($val['refund_fee'],$commissionFee));
                    $data['settlement_fee'] = -$settlementFee;
                }
                else
                {
                    //计算用户最终实际付款的金额，付款金额-退款金额
                    $payment = $objLibMath->number_minus(array($val['payment'],$val['refund_fee']));
                    //计算平台提取的佣金
                    $data['commission_fee'] = $objLibMath->number_multiple(array($payment,$val['cat_service_rate']));
                    //计算结算给商家的金额
                    $data['settlement_fee'] = $objLibMath->number_minus(array($val['payment'],$data['commission_fee']));
                }

                if(!$objMdlSettleDetail->save($data))
                {
                    return false;
                }
            }
        }

        return true;
    }

    public function doConfirm($settlementNo, $status)
    {
        if($status=='2')
        {
            $status = '2';
        }
        else
        {
            return fase;
        }
        return app::get('sysclearing')->model('settlement')->update(array('settlement_status'=>$status),array('settlement_no'=>$settlementNo));
    }


}
