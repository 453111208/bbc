<?php

/**
 * ShopEx licence
 *
 * @copyright  Copyright (c) 2005-2010 ShopEx Technologies Inc. (http://www.shopex.cn)
 * @license  http://ecos.shopex.cn/ ShopEx License
 */


class ectools_task
{

    public function post_install()
    {
        logger::info('Initial ectools');
        kernel::single('base_initial', 'ectools')->init();
    }//End Function

    public function post_update( $dbver )
    {
        if($dbver['dbver'] < 0.5)
        {
            $db = app::get('sysitem')->database();
            $paymentBill = $db->executeQuery('SELECT tids,trade_own_money,payment_id,status,payed_time,created_time,modified_time,user_id FROM ectools_payments');
            foreach($paymentBill as $value)
            {
                if($value['tids'] && $value['trade_own_money'])
                {
                    $trade = unserialize($value['trade_own_money']);
                    foreach($trade as $tid=>$payment)
                    {
                        $db->executeUpdate('insert into ectools_trade_paybill(payment_id,tid,status,payment,user_id,payed_time,created_time,modified_time) value (?,?,?,?,?,?,?,?)',[$value['payment_id'],$tid,$value['status'],$payment,$value['user_id'],$value['payed_time'],$value['created_time'],$value['modified_time']]);
                    }
                }
            }
        }
    }
}//End Class
