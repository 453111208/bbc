<?php
class ectools_payment_pay{
    public function create(&$sdf, &$msg='')
    {
        //最初支付单默认数据
        //获取当前发起支付的用户名
        self::_checkPostData($sdf);
        $time = time();
        $sdf['created_time'] = $time;
        $sdf['modified_time'] = $time;

        $objModelPay = app::get('ectools')->model('payments');
        $is_save = $objModelPay->save($sdf,null);

        if (!$is_save)
        {
            throw new \LogicException(app::get('ectools')->_('支付信息更新失败！'));
            return false;
        }
        return true;
    }

    public function update(&$sdf, &$msg='')
    {
        // 修改支付单是和中心的交互
        $payedMoney = $sdf['cur_money'];
        self::_checkPostData($sdf);
        if($payedMoney != $sdf['cur_money'])
        {
             throw new \LogicException(app::get('ectools')->_('支付单修改失败！支付金额有异常'));
            return false;
        }

        if($sdf['status'] == "succ") $sdf['payed_time'] = time();
        $objPayments = app::get('ectools')->model('payments');

        $filter = array(
            'payment_id' => $sdf['payment_id'],
            'status|noequal' => 'succ',
        );
        $is_save = $objPayments->update($sdf, $filter);

        if ($is_save)
        {
            return true;
        }
        else
        {
            throw new \LogicException(app::get('ectools')->_('支付单修改失败！'));
            return false;
        }
    }

    private function _payMethod(&$params)
    {
        //处理支付方式相关数据
        if($params['pay_app_id'])
        {
            $objPaymentcfgs = app::get('ectools')->model('payment_cfgs');
            $arrPyMethod = $objPaymentcfgs->getPaymentInfo($params['pay_app_id']);
            $class_name = "";
            $obj_app_plugins = kernel::servicelist("ectools_payment.ectools_mdl_payment_cfgs");
            foreach ($obj_app_plugins as $obj_app)
            {
                $app_class_name = get_class($obj_app);
                $arr_class_name = explode('_', $app_class_name);
                if (isset($arr_class_name[count($arr_class_name)-1]) && $arr_class_name[count($arr_class_name)-1])
                {
                    if ($arr_class_name[count($arr_class_name)-1] == $params['pay_app_id'])
                    {
                        $pay_app_ins = $obj_app;
                        $class_name = $app_class_name;
                    }
                }
                else
                {
                    if ($app_class_name == $params['pay_app_id'])
                    {
                        $pay_app_ins = $obj_app;
                        $class_name = $app_class_name;
                    }
                }
            }
            if(!$params['pay_type'])
            {
                $params['pay_type'] = ($arrPyMethod['app_pay_type'] == 'true') ? 'online' : 'offline';
            }

            if($params['mallName'])
            {
                $params['account'] = $params['mallName'] ;
            }

            if(!$params['bank'])
            {
                $params['bank'] = $arrPyMethod['app_display_name'];
            }

            if(!$params['account'])
            {
                $params['account'] = $params['bank'] ;
            }

            $params['pay_name'] = $arrPyMethod['app_display_name'];
            $params['pay_ver'] = $arrPyMethod['app_version'];
        }
    }

    private function _checkPostData(&$sdf)
    {
        if( !$sdf['pay_account'] && !$sdf['user_id'])
        {
            $sdf['pay_account'] = app::get('ectools')->_('非会员顾客');
        }

        if(!$sdf['user_id'])
        {
            $sdf['user_id'] = '0';
        }

        if(!$sdf['op_id'])
        {
            $sdf['op_id'] =  $sdf['user_id'];
        }

        if(!$sdf['ip'])
        {
            $sdf['ip'] = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : $_SERVER['HTTP_HOST'];
        }
        $sdf['cur_money'] = $sdf['money'];

        self::_payMethod($sdf);
    }
}
