<?php
/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */

class ectools_mdl_payments extends dbeav_model{

    var $defaultOrder = array('payed_time','DESC');

    /**
     * 得到唯一的payment id 总共20位 前十五位为订单号
     * @params null
     * @return string payment id
     */
    public function genId($order_id=null){
        if( is_null($order_id) ){
            trigger_error(app::get('ectools')->_("订单号不能为空！"), E_USER_ERROR);exit;
        }
        $order_id = str_pad($order_id,15,time());
        $i = rand(0,99999);
        do{
            if(99999==$i){
                $i=0;
            }
            $i++;
            $payment_id = $order_id.str_pad($i,5,'0',STR_PAD_LEFT);
            $row = $this->getRow('payment_id',array('payment_id'=>$payment_id));
        }while($row);
        return $payment_id;
    }

    /**
     * 模板统一保存的方法
     * @params array - 需要保存的支付信息
     * @params boolean - 是否需要强制保存
     * @return boolean - 保存的成功与否的进程
     */
    public function save(&$data,$mustUpdate = null, $mustInsert=false)
    {
        // 异常处理
        if (!isset($data) || !$data || !is_array($data))
        {
            trigger_error(app::get('ectools')->_("支付单信息不能为空！"), E_USER_ERROR);exit;
        }

        $sdf = array();

        // 支付数据列表
        $background = true;//后台 todo

        $payment_data = $data;
        $sdf_payment = $this->getRow('*',array('payment_id'=>$data['payment_id']));
        if ($sdf_payment)
        {
            if($sdf_payment['status'] == $data['status']
                || ($sdf_payment['status'] != 'progress' && $sdf_payment['status'] != 'ready')){
                return true;
            }
        }
        $sdf = $data;
        $sdf['status'] = $sdf['status'] ? $sdf['status'] : 'ready';

        // 保存支付信息（可能是退款信息）
        $is_succ = parent::save($sdf,$mustUpdate,$mustInsert);

        return $is_succ;
    }
}


