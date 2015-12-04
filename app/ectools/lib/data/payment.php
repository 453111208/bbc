<?php
class ectools_data_payment{

    /**
     * @brief 获取现有的支付方式
     *
     * @param $cur 货币
     *
     * @return array
     */
    public function getPayments($cur,$paytype)
    {
        $objPayment = app::get('ectools')->model('payment_cfgs');
        $payment = $objPayment->getListByCode($cur,$paytype);

        if(!$payment) return "";
        foreach($payment as $key=>$val)
        {
            $staticDir = app::get('ectools')->res_url;
            $img = $staticDir."/images/".$val['app_id'].".gif";
            $payment[$key]['img'] = $img;
        }
        return $payment;
    }

    /**
     * @brief 获取支付单信息
     *
     * @param $paymentId 支付单id
     *
     * @return array
     */
    public function getPaymentInfo($rows,$filter)
    {
        $objMdlPayment = app::get('ectools')->model('payments');
        $payment = $objMdlPayment->getRow($rows,$filter);
        return $payment;
    }
}


